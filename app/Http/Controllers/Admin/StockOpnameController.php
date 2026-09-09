<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    /**
     * Daftar Stock Opname
     */
    public function index()
    {
        $opnames = StockOpname::with('user')
            ->withCount('details')
            ->latest()
            ->paginate(10);

        return view(
            'admin.stock-opname.index',
            compact('opnames')
        );
    }

    /**
     * Form Stock Opname Baru
     */
    public function create()
    {
        /*
         * Ambil semua produk aktif.
         *
         * Variant sekarang bersifat dynamic:
         * Variant 1, Variant 2, Variant 3, dst.
         *
         * JANGAN menggunakan:
         * orderBy('color')
         * orderBy('size')
         *
         * karena kolom color dan size sudah tidak digunakan.
         */
        $products = Product::with([
            'variants' => function ($query) {
                $query->orderBy('id');
            }
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.stock-opname.create',
            compact('products')
        );
    }

    /**
     * Simpan Stock Opname sebagai DRAFT
     *
     * Pada tahap ini:
     * - tidak mengubah stok
     * - tidak membuat stock movement
     * - hanya menyimpan hasil pemeriksaan
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'opname_date' => [
                'required',
                'date',
            ],

            'note' => [
                'nullable',
                'string',
                'max:255',
            ],

            'variants' => [
                'required',
                'array',
                'min:1',
            ],

            'variants.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'variants.*.physical_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'variants.*.note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $opname = DB::transaction(function () use ($data) {

            /*
             * Buat Stock Opname sebagai draft.
             */
            $opname = StockOpname::create([
                'user_id' => Auth::id(),
                'opname_date' => $data['opname_date'],
                'status' => 'draft',
                'note' => $data['note'] ?? null,
            ]);

            /*
             * Simpan setiap variant yang diperiksa.
             */
            foreach ($data['variants'] as $row) {

                $variant = ProductVariant::findOrFail(
                    $row['product_variant_id']
                );

                /*
                 * Ambil stok sistem saat opname dibuat.
                 */
                $systemStock = (int) $variant->stock;

                /*
                 * Ambil stok fisik dari hasil pemeriksaan.
                 */
                $physicalStock = (int) $row['physical_stock'];

                /*
                 * Hitung selisih.
                 *
                 * Positif = stok fisik lebih banyak
                 * Negatif = stok fisik lebih sedikit
                 */
                $difference = $physicalStock - $systemStock;

                StockOpnameDetail::create([
                    'stock_opname_id' => $opname->id,

                    'product_id' => $variant->product_id,

                    'product_variant_id' => $variant->id,

                    'system_stock' => $systemStock,

                    'physical_stock' => $physicalStock,

                    'difference' => $difference,

                    'note' => $row['note'] ?? null,
                ]);
            }

            return $opname;
        });

        return redirect()
            ->route(
                'admin.stock-opname.show',
                $opname
            )
            ->with(
                'success',
                'Stock opname berhasil dibuat sebagai draft.'
            );
    }

    /**
     * Form Edit Stock Opname
     *
     * Hanya draft yang boleh diedit.
     */
    public function edit(StockOpname $stockOpname)
    {
        /*
         * Completed tidak boleh diedit.
         */
        if ($stockOpname->status === 'completed') {
            return redirect()
                ->route(
                    'admin.stock-opname.show',
                    $stockOpname
                )
                ->with(
                    'error',
                    'Stock opname yang sudah selesai tidak dapat diedit.'
                );
        }

        /*
         * Load detail dan relasi.
         */
        $stockOpname->load([
            'details.product',
            'details.variant',
            'user',
        ]);

        /*
         * Ambil produk aktif beserta
         * seluruh dynamic variants.
         */
        $products = Product::with([
            'variants' => function ($query) {
                $query->orderBy('id');
            }
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
         * Mapping detail berdasarkan variant.
         *
         * Contoh:
         *
         * [
         *     variant_id => detail,
         *     variant_id => detail,
         * ]
         */
        $details = $stockOpname->details
            ->keyBy('product_variant_id');

        return view(
            'admin.stock-opname.edit',
            compact(
                'stockOpname',
                'products',
                'details'
            )
        );
    }

    /**
     * Update / Selesaikan Stock Opname
     *
     * Draft -> Completed
     *
     * Pada proses ini:
     *
     * 1. Ambil stok sistem terbaru
     * 2. Bandingkan dengan stok fisik
     * 3. Simpan detail
     * 4. Buat Stock Movement jika ada selisih
     * 5. Update stok variant
     * 6. Update total stok product
     * 7. Ubah status menjadi completed
     */
    public function update(
        Request $request,
        StockOpname $stockOpname
    ) {
        /*
         * Completed tidak boleh diubah.
         */
        if ($stockOpname->status === 'completed') {
            return redirect()
                ->route(
                    'admin.stock-opname.show',
                    $stockOpname
                )
                ->with(
                    'error',
                    'Stock opname yang sudah selesai tidak dapat diubah.'
                );
        }

        /*
         * Validasi input.
         */
        $data = $request->validate([
            'opname_date' => [
                'required',
                'date',
            ],

            'note' => [
                'nullable',
                'string',
                'max:255',
            ],

            'variants' => [
                'required',
                'array',
                'min:1',
            ],

            'variants.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'variants.*.physical_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'variants.*.note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use (
            $data,
            $stockOpname
        ) {

            /*
             * Lock Stock Opname.
             *
             * Tujuannya mencegah dua proses
             * menyelesaikan opname yang sama
             * secara bersamaan.
             */
            $opname = StockOpname::lockForUpdate()
                ->findOrFail($stockOpname->id);

            /*
             * Double protection.
             *
             * Kalau sudah completed,
             * proses dihentikan.
             */
            if ($opname->status === 'completed') {
                abort(
                    422,
                    'Stock opname sudah selesai dan tidak dapat diubah.'
                );
            }

            /*
             * Update informasi Stock Opname.
             */
            $opname->update([
                'opname_date' => $data['opname_date'],
                'note' => $data['note'] ?? null,
            ]);

            /*
             * Hapus detail lama.
             *
             * Aman dilakukan karena status masih draft.
             */
            StockOpnameDetail::where(
                'stock_opname_id',
                $opname->id
            )->delete();

            /*
             * Simpan product ID yang terdampak.
             */
            $affectedProductIds = [];

            /*
             * Proses setiap variant.
             */
            foreach ($data['variants'] as $row) {

                /*
                 * Lock variant.
                 *
                 * Ini penting supaya stok sistem
                 * yang digunakan adalah stok terbaru
                 * dan tidak berubah di tengah proses.
                 */
                $variant = ProductVariant::lockForUpdate()
                    ->findOrFail(
                        $row['product_variant_id']
                    );

                /*
                 * Stok sistem terbaru.
                 */
                $systemStock = (int) $variant->stock;

                /*
                 * Stok fisik hasil pengecekan.
                 */
                $physicalStock = (int) $row['physical_stock'];

                /*
                 * Hitung selisih.
                 */
                $difference =
                    $physicalStock - $systemStock;

                /*
                 * Simpan detail final.
                 */
                StockOpnameDetail::create([
                    'stock_opname_id' =>
                        $opname->id,

                    'product_id' =>
                        $variant->product_id,

                    'product_variant_id' =>
                        $variant->id,

                    'system_stock' =>
                        $systemStock,

                    'physical_stock' =>
                        $physicalStock,

                    'difference' =>
                        $difference,

                    'note' =>
                        $row['note'] ?? null,
                ]);

                /*
                 * Kalau ada selisih,
                 * buat Stock Movement.
                 */
                if ($difference !== 0) {

                    /*
                     * Selisih positif:
                     *
                     * Stok fisik > stok sistem
                     *
                     * Artinya stok bertambah.
                     */
                    $movementType =
                        $difference > 0
                            ? 'in'
                            : 'out';

                    /*
                     * Quantity harus selalu positif.
                     */
                    $movementQuantity =
                        abs($difference);

                    /*
                     * Buat Stock Movement.
                     */
                    StockMovement::create([
                        'product_id' =>
                            $variant->product_id,

                        'product_variant_id' =>
                            $variant->id,

                        'user_id' =>
                            Auth::id(),

                        'type' =>
                            $movementType,

                        'quantity' =>
                            $movementQuantity,

                        'source' =>
                            'opname',

                        'note' =>
                            'Penyesuaian stock opname #' .
                            $opname->id .
                            (
                                !empty($row['note'])
                                    ? ' - ' . $row['note']
                                    : ''
                            ),
                    ]);

                    /*
                     * Update stok variant
                     * menjadi stok fisik sebenarnya.
                     */
                    $variant->update([
                        'stock' =>
                            $physicalStock,
                    ]);
                }

                /*
                 * Simpan product ID.
                 *
                 * Nanti digunakan untuk menghitung
                 * ulang total stok product.
                 */
                $affectedProductIds[] =
                    $variant->product_id;
            }

            /*
             * Update total stok setiap product
             * yang terkena Stock Opname.
             */
            foreach (
                array_unique($affectedProductIds)
                as $productId
            ) {

                /*
                 * Jumlahkan seluruh stok variant
                 * milik product.
                 */
                $totalStock =
                    ProductVariant::where(
                        'product_id',
                        $productId
                    )->sum('stock');

                /*
                 * Simpan total stok ke products.stock.
                 */
                Product::where(
                    'id',
                    $productId
                )->update([
                    'stock' =>
                        $totalStock,
                ]);
            }

            /*
             * Tandai Stock Opname selesai.
             */
            $opname->update([
                'status' => 'completed',
            ]);
        });

        return redirect()
            ->route(
                'admin.stock-opname.show',
                $stockOpname
            )
            ->with(
                'success',
                'Stock opname berhasil diperbarui dan stok telah disesuaikan.'
            );
    }

    /**
     * Detail Stock Opname
     */
    public function show(StockOpname $stockOpname)
    {
        /*
         * Load semua relasi yang dibutuhkan
         * oleh halaman show.
         */
        $stockOpname->load([
            'details.product',
            'details.variant',
            'user',
        ]);

        return view(
            'admin.stock-opname.show',
            compact('stockOpname')
        );
    }
}
