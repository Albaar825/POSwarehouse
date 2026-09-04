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
     * Daftar stock opname
     */
    public function index()
    {
        $opnames = StockOpname::with('user')
            ->withCount('details')
            ->latest()
            ->paginate(10);

        return view('admin.stock-opname.index', compact('opnames'));
    }

    /**
     * Form stock opname baru
     */
    public function create()
    {
        $products = Product::with([
            'variants' => function ($q) {
                $q->orderBy('color')
                    ->orderBy('size');
            }
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.stock-opname.create', compact('products'));
    }

    /**
     * Simpan stock opname sebagai DRAFT
     *
     * Pada tahap ini:
     * - belum mengubah stok
     * - belum membuat stock movement
     * - hanya menyimpan hasil pemeriksaan
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'opname_date' => 'required|date',
            'note' => 'nullable|string|max:255',

            'variants' => 'required|array|min:1',

            'variants.*.product_variant_id' =>
                'required|exists:product_variants,id',

            'variants.*.physical_stock' =>
                'required|integer|min:0',

            'variants.*.note' =>
                'nullable|string|max:255',
        ]);

        $opname = DB::transaction(function () use ($data) {

            /*
             * Buat opname sebagai DRAFT.
             *
             * Belum ada perubahan stok pada tahap ini.
             */
            $opname = StockOpname::create([
                'user_id' => Auth::id(),
                'opname_date' => $data['opname_date'],
                'status' => 'draft',
                'note' => $data['note'] ?? null,
            ]);

            /*
             * Simpan semua detail pemeriksaan.
             */
            foreach ($data['variants'] as $row) {

                $variant = ProductVariant::findOrFail(
                    $row['product_variant_id']
                );

                $systemStock = (int) $variant->stock;
                $physicalStock = (int) $row['physical_stock'];

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
            ->route('admin.stock-opname.show', $opname)
            ->with(
                'success',
                'Stock opname berhasil dibuat sebagai draft.'
            );
    }

    /**
     * Form edit stock opname
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
         * Ambil detail dan relasi.
         */
        $stockOpname->load([
            'details.product',
            'details.variant',
            'user',
        ]);

        /*
         * Ambil produk aktif beserta variant.
         */
        $products = Product::with([
            'variants' => function ($q) {
                $q->orderBy('color')
                    ->orderBy('size');
            }
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
         * Mapping detail berdasarkan product_variant_id.
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
     * Update / Selesaikan stock opname
     *
     * Draft -> Completed
     *
     * Pada tahap ini:
     * - hitung selisih
     * - buat stock movement
     * - update stok variant
     * - update total stok product
     * - ubah status menjadi completed
     */
    public function update(
        Request $request,
        StockOpname $stockOpname
    ) {
        /*
         * Jangan izinkan update completed.
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

        $data = $request->validate([
            'opname_date' => 'required|date',

            'note' => 'nullable|string|max:255',

            'variants' => 'required|array|min:1',

            'variants.*.product_variant_id' =>
                'required|exists:product_variants,id',

            'variants.*.physical_stock' =>
                'required|integer|min:0',

            'variants.*.note' =>
                'nullable|string|max:255',
        ]);

        DB::transaction(function () use (
            $data,
            $stockOpname
        ) {

            /*
             * Lock stock opname.
             */
            $opname = StockOpname::lockForUpdate()
                ->findOrFail($stockOpname->id);

            /*
             * Pastikan masih draft.
             */
            if ($opname->status === 'completed') {
                abort(
                    422,
                    'Stock opname sudah selesai dan tidak dapat diubah.'
                );
            }

            /*
             * Update informasi opname.
             */
            $opname->update([
                'opname_date' => $data['opname_date'],
                'note' => $data['note'] ?? null,
            ]);

            /*
             * Detail lama dihapus karena
             * opname masih dalam status draft.
             */
            StockOpnameDetail::where(
                'stock_opname_id',
                $opname->id
            )->delete();

            $affectedProductIds = [];

            /*
             * Proses setiap variant.
             */
            foreach ($data['variants'] as $row) {

                /*
                 * Lock variant untuk mencegah
                 * perubahan stok bersamaan.
                 */
                $variant = ProductVariant::lockForUpdate()
                    ->findOrFail(
                        $row['product_variant_id']
                    );

                /*
                 * Ambil stok sistem terbaru.
                 */
                $systemStock = (int) $variant->stock;

                $physicalStock =
                    (int) $row['physical_stock'];

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
                 * Jika ada selisih,
                 * buat stock movement.
                 */
                if ($difference !== 0) {

                    StockMovement::create([
                        'product_id' =>
                            $variant->product_id,

                        'product_variant_id' =>
                            $variant->id,

                        'user_id' =>
                            Auth::id(),

                        'type' =>
                            $difference > 0
                                ? 'in'
                                : 'out',

                        'quantity' =>
                            abs($difference),

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
                     * menjadi stok fisik.
                     */
                    $variant->update([
                        'stock' =>
                            $physicalStock,
                    ]);
                }

                $affectedProductIds[] =
                    $variant->product_id;
            }

            /*
             * Sinkronisasi total stok product.
             */
            foreach (
                array_unique($affectedProductIds)
                as $productId
            ) {

                $total =
                    ProductVariant::where(
                        'product_id',
                        $productId
                    )->sum('stock');

                Product::where(
                    'id',
                    $productId
                )->update([
                    'stock' => $total,
                ]);
            }

            /*
             * Setelah proses selesai,
             * ubah status menjadi completed.
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
     * Detail stock opname
     */
    public function show(StockOpname $stockOpname)
    {
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
