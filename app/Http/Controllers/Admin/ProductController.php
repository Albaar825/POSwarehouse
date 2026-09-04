<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Menampilkan semua produk
     */
    public function index(Request $request)
    {
        $products = Product::with([
                'category',
                'variants.images',
            ])
            ->when(
                $request->search,
                fn ($q) =>
                    $q->where(
                        'name',
                        'like',
                        '%' . $request->search . '%'
                    )
            )
            ->latest()
            ->get();

        return view(
            'admin.products.index',
            compact('products')
        );
    }

    /**
     * Form tambah produk
     */
    public function create()
    {
        $categories = Category::all();

        return view(
            'admin.products.create',
            compact('categories')
        );
    }

    /**
     * Simpan produk baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:255',
                'unique:products,sku',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'purchase_price' => [
                'required',
                'integer',
                'min:0',
            ],

            'price' => [
                'required',
                'integer',
                'min:0',
            ],

            'min_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'unit' => [
                'required',
                'string',
                'max:20',
            ],

            /*
             * VARIANTS
             */
            'variants' => [
                'required',
                'array',
                'min:1',
            ],

            'variants.*.color' => [
                'nullable',
                'string',
                'max:50',
            ],

            'variants.*.size' => [
                'nullable',
                'string',
                'max:50',
            ],

            'variants.*.stock' => [
                'required',
                'integer',
                'min:0',
            ],

            /*
             * IMAGES PER VARIANT
             */
            'variants.*.images' => [
                'nullable',
                'array',
                'max:10',
            ],

            'variants.*.images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        DB::transaction(function () use ($request, $data) {

            /*
             * TOTAL STOCK
             */
            $totalStock = collect($data['variants'])
                ->sum('stock');

            /*
             * CREATE PRODUCT
             */
            $product = Product::create([
                'category_id' => $data['category_id'] ?? null,

                'sku' => $data['sku'],

                'name' => $data['name'],

                'image' => null,

                'purchase_price' => $data['purchase_price'],

                'price' => $data['price'],

                'min_stock' => $data['min_stock'],

                'unit' => $data['unit'],

                'stock' => $totalStock,

                'is_active' => true,
            ]);

            /*
             * CREATE VARIANTS
             */
            foreach ($data['variants'] as $index => $variantData) {

                $variant = ProductVariant::create([
                    'product_id' => $product->id,

                    'color' =>
                        $variantData['color'] ?? null,

                    'size' =>
                        $variantData['size'] ?? null,

                    'sku_variant' =>
                        $product->sku . '-' . ($index + 1),

                    'stock' =>
                        $variantData['stock'],
                ]);

                /*
                 * SIMPAN SEMUA GAMBAR VARIANT
                 */
                $files = $request->file(
                    "variants.$index.images",
                    []
                );

                foreach ($files as $imageIndex => $file) {

                    $path = $file->store(
                        'products/variants',
                        'public'
                    );

                    ProductVariantImage::create([
                        'product_variant_id' =>
                            $variant->id,

                        'image' =>
                            $path,

                        /*
                         * GAMBAR PERTAMA = PRIMARY
                         */
                        'is_primary' =>
                            $imageIndex === 0,

                        'sort_order' =>
                            $imageIndex,
                    ]);
                }

                /*
                 * ==========================================================
                 * CATAT STOK AWAL SEBAGAI STOK MASUK
                 * ==========================================================
                 *
                 * Contoh:
                 *
                 * Hitam / M = 10
                 *
                 * Maka otomatis:
                 *
                 * type     = in
                 * quantity = 10
                 * source   = product_creation
                 *
                 */

                if ($variantData['stock'] > 0) {

                    StockMovement::create([
                        'product_id' =>
                            $product->id,

                        'product_variant_id' =>
                            $variant->id,

                        'user_id' =>
                            Auth::id(),

                        'type' =>
                            'in',

                        'quantity' =>
                            $variantData['stock'],

                        'source' =>
                            'product_creation',

                        'transaction_id' =>
                            null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Barang, varian, gambar, dan stok awal berhasil ditambahkan.'
            );
    }

    /**
     * Detail produk
     */
    public function show(Product $product)
    {
        $product->load([
            'category',
            'variants.images',
        ]);

        return view(
            'admin.products.show',
            compact('product')
        );
    }

    /**
     * Form edit produk
     */
    public function edit(Product $product)
    {
        $categories = Category::all();

        $product->load([
            'variants.images',
        ]);

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }

    /**
     * Update produk
     */
    public function update(
        Request $request,
        Product $product
    ) {
        $data = $request->validate([
            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:255',
                'unique:products,sku,' . $product->id,
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'purchase_price' => [
                'required',
                'integer',
                'min:0',
            ],

            'price' => [
                'required',
                'integer',
                'min:0',
            ],

            'min_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'unit' => [
                'required',
                'string',
                'max:20',
            ],

            /*
             * STATUS
             */
            'is_active' => [
                'nullable',
                'boolean',
            ],

            /*
             * VARIANTS
             */
            'variants' => [
                'required',
                'array',
                'min:1',
            ],

            'variants.*.id' => [
                'nullable',
                'integer',
                'exists:product_variants,id',
            ],

            'variants.*.color' => [
                'nullable',
                'string',
                'max:50',
            ],

            'variants.*.size' => [
                'nullable',
                'string',
                'max:50',
            ],

            'variants.*.stock' => [
                'required',
                'integer',
                'min:0',
            ],

            /*
             * IMAGES BARU
             */
            'variants.*.images' => [
                'nullable',
                'array',
                'max:10',
            ],

            'variants.*.images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $data,
            $product
        ) {

            /*
             * ==========================================================
             * UPDATE PRODUCT
             * ==========================================================
             */

            $product->update([
                'category_id' =>
                    $data['category_id'] ?? null,

                'sku' =>
                    $data['sku'],

                'name' =>
                    $data['name'],

                'purchase_price' =>
                    $data['purchase_price'],

                'price' =>
                    $data['price'],

                'min_stock' =>
                    $data['min_stock'],

                'unit' =>
                    $data['unit'],

                'is_active' =>
                    $request->boolean('is_active'),
            ]);

            /*
             * ID VARIANT YANG MASIH DIPAKAI
             */
            $keepIds = [];

            /*
             * ==========================================================
             * UPDATE / CREATE VARIANTS
             * ==========================================================
             */
            foreach (
                $data['variants']
                as $index => $variantData
            ) {

                /*
                 * ======================================================
                 * UPDATE VARIANT LAMA
                 * ======================================================
                 */
                if (!empty($variantData['id'])) {

                    $variant = ProductVariant::where(
                            'product_id',
                            $product->id
                        )
                        ->where(
                            'id',
                            $variantData['id']
                        )
                        ->firstOrFail();

                    /*
                     * Simpan stok lama sebelum diubah
                     */
                    $oldStock = (int) $variant->stock;

                    /*
                     * Stok baru dari form
                     */
                    $newStock = (int) $variantData['stock'];

                    /*
                     * Selisih stok
                     *
                     * contoh:
                     * 10 -> 15 = +5
                     * 10 -> 7  = -3
                     */
                    $stockDifference =
                        $newStock - $oldStock;

                    /*
                     * UPDATE VARIANT
                     */
                    $variant->update([
                        'color' =>
                            $variantData['color'] ?? null,

                        'size' =>
                            $variantData['size'] ?? null,

                        'stock' =>
                            $newStock,
                    ]);

                    /*
                     * ==================================================
                     * CATAT PERUBAHAN STOK
                     * ==================================================
                     */
                    if ($stockDifference != 0) {

                        StockMovement::create([
                            'product_id' =>
                                $product->id,

                            'product_variant_id' =>
                                $variant->id,

                            'user_id' =>
                                Auth::id(),

                            'type' =>
                                $stockDifference > 0
                                    ? 'in'
                                    : 'out',

                            'quantity' =>
                                abs($stockDifference),

                            'source' =>
                                'product_update',

                            'transaction_id' =>
                                null,
                        ]);
                    }

                } else {

                    /*
                     * ==================================================
                     * CREATE VARIANT BARU
                     * ==================================================
                     */

                    $variant = ProductVariant::create([
                        'product_id' =>
                            $product->id,

                        'color' =>
                            $variantData['color'] ?? null,

                        'size' =>
                            $variantData['size'] ?? null,

                        'sku_variant' =>
                            $product->sku .
                            '-' .
                            ($index + 1) .
                            '-' .
                            uniqid(),

                        'stock' =>
                            $variantData['stock'],
                    ]);

                    /*
                     * ==================================================
                     * STOK VARIANT BARU = STOK MASUK
                     * ==================================================
                     */

                    if ($variantData['stock'] > 0) {

                        StockMovement::create([
                            'product_id' =>
                                $product->id,

                            'product_variant_id' =>
                                $variant->id,

                            'user_id' =>
                                Auth::id(),

                            'type' =>
                                'in',

                            'quantity' =>
                                $variantData['stock'],

                            'source' =>
                                'product_update',

                            'transaction_id' =>
                                null,
                        ]);
                    }
                }

                $keepIds[] = $variant->id;

                /*
                 * ======================================================
                 * SIMPAN GAMBAR BARU
                 * ======================================================
                 */

                $files = $request->file(
                    "variants.$index.images",
                    []
                );

                if (!empty($files)) {

                    /*
                     * Cari urutan gambar terakhir
                     */
                    $lastSortOrder =
                        ProductVariantImage::where(
                            'product_variant_id',
                            $variant->id
                        )->max('sort_order');

                    /*
                     * Apakah sudah punya primary?
                     */
                    $hasPrimary =
                        ProductVariantImage::where(
                            'product_variant_id',
                            $variant->id
                        )
                        ->where(
                            'is_primary',
                            true
                        )
                        ->exists();

                    foreach (
                        $files
                        as $fileIndex => $file
                    ) {

                        $path = $file->store(
                            'products/variants',
                            'public'
                        );

                        ProductVariantImage::create([
                            'product_variant_id' =>
                                $variant->id,

                            'image' =>
                                $path,

                            /*
                             * Jika belum ada primary,
                             * gambar pertama menjadi primary.
                             */
                            'is_primary' =>
                                !$hasPrimary &&
                                $fileIndex === 0,

                            'sort_order' =>
                                $lastSortOrder +
                                $fileIndex +
                                1,
                        ]);
                    }
                }
            }

            /*
             * ==========================================================
             * HAPUS VARIANT YANG DIHILANGKAN DARI FORM
             * ==========================================================
             */

            $oldVariants = ProductVariant::with('images')
                ->where(
                    'product_id',
                    $product->id
                )
                ->whereNotIn(
                    'id',
                    $keepIds
                )
                ->get();

            foreach ($oldVariants as $oldVariant) {

                /*
                 * Hapus file fisik gambar
                 */
                foreach (
                    $oldVariant->images
                    as $image
                ) {

                    if (
                        Storage::disk('public')
                            ->exists($image->image)
                    ) {

                        Storage::disk('public')
                            ->delete($image->image);
                    }
                }

                /*
                 * Hapus variant
                 */
                $oldVariant->delete();
            }

            /*
             * ==========================================================
             * HITUNG ULANG TOTAL STOCK PRODUK
             * ==========================================================
             */

            $totalStock = ProductVariant::where(
                    'product_id',
                    $product->id
                )
                ->sum('stock');

            $product->update([
                'stock' =>
                    $totalStock,
            ]);
        });

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Barang, varian, stok, dan gambar berhasil diupdate.'
            );
    }

    /**
     * Hapus produk
     */
    public function destroy(Product $product)
    {
        /*
         * Load semua variant + gambar
         */
        $product->load([
            'variants.images',
        ]);

        /*
         * Hapus gambar produk lama
         * jika masih ada dari sistem sebelumnya
         */
        if ($product->image) {

            if (
                Storage::disk('public')
                    ->exists($product->image)
            ) {

                Storage::disk('public')
                    ->delete($product->image);
            }
        }

        /*
         * Hapus semua gambar variant
         */
        foreach (
            $product->variants
            as $variant
        ) {

            foreach (
                $variant->images
                as $image
            ) {

                if (
                    Storage::disk('public')
                        ->exists($image->image)
                ) {

                    Storage::disk('public')
                        ->delete($image->image);
                }
            }
        }

        /*
         * Hapus product.
         *
         * Variant otomatis terhapus karena
         * cascadeOnDelete.
         *
         * Image variant juga otomatis terhapus
         * karena cascadeOnDelete.
         */
        $product->delete();

        return back()
            ->with(
                'success',
                'Barang berhasil dihapus.'
            );
    }
}
