<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $products = Product::with([
            'category',
            'variants',
        ])
            ->when(
                $request->search,
                function ($query) use ($request) {

                    $query->where(function ($q) use ($request) {

                        $q->where(
                            'name',
                            'like',
                            '%' . $request->search . '%'
                        )

                        ->orWhere(
                            'sku',
                            'like',
                            '%' . $request->search . '%'
                        );

                    });

                }
            )
            ->latest()
            ->get();

        return view(
            'admin.products.index',
            compact('products')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories =
            Category::orderBy('name')->get();

        return view(
            'admin.products.create',
            compact('categories')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated =
            $this->validateProduct($request);


        DB::transaction(function () use (
            $request,
            $validated
        ) {

            /*
            |--------------------------------------------------------------------------
            | PROCESS VARIANT GROUP
            |--------------------------------------------------------------------------
            */

            $variantGroups =
                $this->processVariantGroups(
                    $request,
                    $request->input(
                        'variant_groups',
                        []
                    )
                );


            $this->validateVariantGroupStructure(
                $variantGroups
            );


            /*
            |--------------------------------------------------------------------------
            | VARIANT
            |--------------------------------------------------------------------------
            */

            $variants =
                $request->input(
                    'variants',
                    []
                );


            if (
                !empty($variantGroups)
                &&
                empty($variants)
            ) {

                throw ValidationException::withMessages([
                    'variants' =>
                        'Silakan generate kombinasi variant terlebih dahulu.',
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | TOTAL STOCK AWAL
            |--------------------------------------------------------------------------
            */

            $totalStock =
                collect($variants)
                    ->sum(function ($variant) {

                        return (int) (
                            $variant['stock'] ?? 0
                        );

                    });


            /*
            |--------------------------------------------------------------------------
            | CREATE PRODUCT
            |--------------------------------------------------------------------------
            */

            $product =
                Product::create([

                    'category_id' =>
                        $validated['category_id']
                        ?? null,

                    'sku' =>
                        $validated['sku'],

                    'name' =>
                        $validated['name'],

                    'purchase_price' =>
                        $validated['purchase_price']
                        ?? 0,

                    'price' =>
                        $validated['price']
                        ?? 0,

                    'stock' =>
                        $totalStock,

                    'min_stock' =>
                        $validated['min_stock']
                        ?? 0,

                    'unit' =>
                        $validated['unit']
                        ?? 'pcs',

                    'variant_groups' =>
                        $variantGroups,

                    'is_active' =>
                        $validated['is_active']
                        ?? true,

                ]);


            /*
            |--------------------------------------------------------------------------
            | GAMBAR UTAMA
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('image')) {

                $product->image =
                    $request
                        ->file('image')
                        ->store(
                            'products',
                            'public'
                        );

                $product->save();

            }


            /*
            |--------------------------------------------------------------------------
            | CREATE VARIANT
            |--------------------------------------------------------------------------
            */

            foreach (
                $variants as $variantData
            ) {

                $attributes =
                    $this->normalizeVariantAttributes(
                        $variantData['attributes']
                        ?? [],
                        $variantGroups
                    );


                $skuVariant =
                    $this->generateVariantSku(
                        $product,
                        $variantData['sku_variant']
                        ?? null,
                        $attributes
                    );


                $stock =
                    (int) (
                        $variantData['stock']
                        ?? 0
                    );


                $price =
                    $this->normalizePrice(
                        $variantData['price']
                        ?? null
                    );


                ProductVariant::create([

                    'product_id' =>
                        $product->getKey(),

                    'attributes' =>
                        $attributes,

                    'price' =>
                        $price,

                    'sku_variant' =>
                        $skuVariant,

                    'stock' =>
                        $stock,

                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | SYNC TOTAL STOCK
            |--------------------------------------------------------------------------
            */

            $product->syncTotalStock();

        });


        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Produk berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Product $product)
    {
        $product->load([
            'category',
            'variants',
            'stockMovements',
        ]);

        return view(
            'admin.products.show',
            compact('product')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        $product->load([
            'category',
            'variants',
        ]);

        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact(
            'product',
            'categories'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct(
            $request,
            $product
        );

        $validated['category_id'] =
            $request->input('category_id');

        $validated['brand_id'] =
            $request->input('brand_id');

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | PRODUCT DATA
            |--------------------------------------------------------------------------
            */

            $product->update([

                'name' =>
                    $validated['name'],

                'slug' =>
                    $validated['slug']
                    ?? $product->slug,

                'description' =>
                    $validated['description']
                    ?? null,

                'category_id' =>
                    $validated['category_id']
                    ?? null,

                'brand_id' =>
                    $validated['brand_id']
                    ?? null,

                'price' =>
                    $validated['price']
                    ?? 0,

                'stock' =>
                    $validated['stock']
                    ?? 0,

                'unit' =>
                    $validated['unit']
                    ?? 'pcs',

                'status' =>
                    $validated['status']
                    ?? $product->status,

            ]);


            /*
            |--------------------------------------------------------------------------
            | PRODUCT IMAGE
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('image')) {

                if ($product->image) {

                    Storage::disk('public')
                        ->delete(
                            $product->image
                        );

                }

                $product->image =
                    $request
                        ->file('image')
                        ->store(
                            'products',
                            'public'
                        );

                $product->save();

            }


            /*
            |--------------------------------------------------------------------------
            | VARIANT GROUPS
            |--------------------------------------------------------------------------
            */

            $variantGroups =
                $request->input(
                    'variant_groups',
                    []
                );

            $product->variant_groups =
                $variantGroups;

            $product->save();


            /*
            |--------------------------------------------------------------------------
            | EXISTING VARIANTS
            |--------------------------------------------------------------------------
            */

            $existingVariantIds = [];


            foreach (
                $request->input(
                    'variants',
                    []
                ) as $variantData
            ) {

                $variantId =
                    $variantData['id']
                    ?? null;


                $attributes =
                    $variantData['attributes']
                    ?? [];


                $price =
                    $variantData['price']
                    ?? 0;


                $skuVariant =
                    $variantData['sku_variant']
                    ?? null;


                /*
                |--------------------------------------------------------------------------
                | EXISTING VARIANT
                |--------------------------------------------------------------------------
                |
                | Stock variant lama TIDAK DIUBAH.
                |
                */

                if ($variantId) {

                    $variant =
                        $product->variants()
                            ->where(
                                'id',
                                $variantId
                            )
                            ->first();


                    if ($variant) {

                        $existingVariantIds[] =
                            $variant->id;


                        $currentStock =
                            (int) $variant->stock;


                        $variant->update([

                            'attributes' =>
                                $attributes,

                            'price' =>
                                $price,

                            'sku_variant' =>
                                $skuVariant,

                            'stock' =>
                                $currentStock,

                        ]);

                    }

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | NEW VARIANT
                |--------------------------------------------------------------------------
                |
                | Stock variant baru diambil dari form.
                |
                */

                $newStock =
                    (int) (
                        $variantData['stock']
                        ?? 0
                    );


                $variant =
                    ProductVariant::create([

                        'product_id' =>
                            $product->getKey(),

                        'attributes' =>
                            $attributes,

                        'price' =>
                            $price,

                        'sku_variant' =>
                            $skuVariant,

                        'stock' =>
                            $newStock,

                    ]);


                $existingVariantIds[] =
                    $variant->id;

            }


            /*
            |--------------------------------------------------------------------------
            | DELETE VARIANTS YANG DIHAPUS
            |--------------------------------------------------------------------------
            */

            if (!empty($existingVariantIds)) {

                $product->variants()
                    ->whereNotIn(
                        'id',
                        $existingVariantIds
                    )
                    ->delete();

            } else {

                $product->variants()
                    ->delete();

            }


            /*
            |--------------------------------------------------------------------------
            | TOTAL STOCK
            |--------------------------------------------------------------------------
            */

            $product->syncTotalStock();


            DB::commit();


            return redirect()
                ->route('products.index')
                ->with(
                    'success',
                    'Produk berhasil diperbarui.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Gagal memperbarui produk: '
                    . $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        DB::transaction(function () use (
            $product
        ) {

            /*
            |--------------------------------------------------------------------------
            | GAMBAR UTAMA
            |--------------------------------------------------------------------------
            */

            if ($product->image) {

                Storage::disk('public')
                    ->delete(
                        $product->image
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | GAMBAR VARIANT
            |--------------------------------------------------------------------------
            */

            $variantImages =
                $this->extractVariantImages(
                    $product->variant_groups
                    ?? []
                );


            foreach (
                $variantImages as $image
            ) {

                if ($image) {

                    Storage::disk('public')
                        ->delete($image);

                }

            }


            /*
            |--------------------------------------------------------------------------
            | DELETE STOCK MOVEMENT
            |--------------------------------------------------------------------------
            */

            $product
                ->stockMovements()
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | DELETE PRODUCT
            |--------------------------------------------------------------------------
            */

            $product->delete();

        });


        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Produk berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function validateProduct(
        Request $request,
        ?Product $product = null
    ): array {

        $productId =
            $product?->getKey();


        return $request->validate([

            /*
            |--------------------------------------------------------------------------
            | PRODUCT
            |--------------------------------------------------------------------------
            */

            'category_id' => [
                'nullable',
                'exists:categories,id',
            ],

            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'products',
                    'sku'
                )->ignore($productId),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'purchase_price' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'price' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'min_stock' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],


            /*
            |--------------------------------------------------------------------------
            | VARIANT GROUP
            |--------------------------------------------------------------------------
            */

            'variant_groups' => [
                'nullable',
                'array',
            ],

            'variant_groups.*.name' => [
                'required',
                'string',
                'max:100',
            ],

            'variant_groups.*.type' => [
                'required',
                'in:parent,child',
            ],

            'variant_groups.*.values' => [
                'required',
                'array',
                'min:1',
            ],

            'variant_groups.*.values.*.name' => [
                'required',
                'string',
                'max:100',
            ],

            'variant_groups.*.values.*.image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'variant_groups.*.values.*.existing_image' => [
                'nullable',
                'string',
                'max:1000',
            ],


            /*
            |--------------------------------------------------------------------------
            | VARIANT COMBINATIONS
            |--------------------------------------------------------------------------
            */

            'variants' => [
                'nullable',
                'array',
            ],

            'variants.*.id' => [
                'nullable',
                'integer',
            ],

            'variants.*.attributes' => [
                'nullable',
                'array',
            ],

            'variants.*.sku_variant' => [
                'nullable',
                'string',
                'max:255',
            ],

            'variants.*.price' => [
                'nullable',
                'integer',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | STOCK VARIANT BARU
            |--------------------------------------------------------------------------
            |
            | Stock hanya digunakan ketika variant baru dibuat.
            | Stock variant lama tetap diambil dari database.
            |
            */

            'variants.*.stock' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESS VARIANT GROUPS
    |--------------------------------------------------------------------------
    */

    protected function processVariantGroups(
        Request $request,
        array $groups,
        array $oldGroups = []
    ): array {

        $processed = [];


        foreach (
            $groups as $groupIndex => $group
        ) {

            $groupName = trim(
                (string) (
                    $group['name']
                    ?? ''
                )
            );


            if ($groupName === '') {
                continue;
            }


            $type =
                $groupIndex === 0
                    ? 'parent'
                    : 'child';


            $values =
                $group['values']
                ?? [];


            $processedValues = [];


            foreach (
                $values as $valueIndex => $value
            ) {

                $valueName = trim(
                    (string) (
                        $value['name']
                        ?? ''
                    )
                );


                if ($valueName === '') {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | CARI GAMBAR LAMA
                |--------------------------------------------------------------------------
                */

                $oldImage =
                    $value['existing_image']
                    ?? null;


                if (!$oldImage) {

                    $oldImage =
                        $this->findOldVariantValueImage(
                            $oldGroups,
                            $groupName,
                            $valueName
                        );

                }


                /*
                |--------------------------------------------------------------------------
                | UPLOAD GAMBAR BARU
                |--------------------------------------------------------------------------
                */

                $newImage = null;


                $imageField =
                    "variant_groups.{$groupIndex}.values.{$valueIndex}.image";


                if ($request->hasFile($imageField)) {

                    $file =
                        $request->file($imageField);


                    if (
                        $file
                        &&
                        $file->isValid()
                    ) {

                        $newImage =
                            $file->store(
                                'products/variants',
                                'public'
                            );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | TENTUKAN GAMBAR FINAL
                |--------------------------------------------------------------------------
                */

                $finalImage =
                    $newImage
                    ?: $oldImage;


                $processedValues[] = [

                    'name' =>
                        $valueName,

                    'image' =>
                        $finalImage,

                ];

            }


            if (
                !empty($processedValues)
            ) {

                $processed[] = [

                    'name' =>
                        $groupName,

                    'type' =>
                        $type,

                    'values' =>
                        $processedValues,

                ];

            }

        }


        return array_values(
            $processed
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CARI GAMBAR VALUE LAMA
    |--------------------------------------------------------------------------
    */

    protected function findOldVariantValueImage(
        array $oldGroups,
        string $groupName,
        string $valueName
    ): ?string {

        foreach (
            $oldGroups as $oldGroup
        ) {

            $oldGroupName =
                trim(
                    (string) (
                        $oldGroup['name']
                        ?? ''
                    )
                );


            if (
                strcasecmp(
                    $oldGroupName,
                    $groupName
                ) !== 0
            ) {
                continue;
            }


            foreach (
                $oldGroup['values']
                ?? [] as $oldValue
            ) {

                if (is_array($oldValue)) {

                    $oldValueName =
                        trim(
                            (string) (
                                $oldValue['name']
                                ?? ''
                            )
                        );


                    if (
                        strcasecmp(
                            $oldValueName,
                            $valueName
                        ) === 0
                    ) {

                        return $oldValue['image']
                            ?? null;

                    }

                }

            }

        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE VARIANT GROUP STRUCTURE
    |--------------------------------------------------------------------------
    */

    protected function validateVariantGroupStructure(
        array $variantGroups
    ): void {

        if (empty($variantGroups)) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | GROUP PERTAMA HARUS PARENT
        |--------------------------------------------------------------------------
        */

        if (
            ($variantGroups[0]['type'] ?? null)
            !== 'parent'
        ) {

            throw ValidationException::withMessages([
                'variant_groups' =>
                    'Variant pertama harus menjadi Variant Utama.',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | GROUP BERIKUTNYA CHILD
        |--------------------------------------------------------------------------
        */

        foreach (
            $variantGroups as $index => $group
        ) {

            if (
                $index > 0
                &&
                ($group['type'] ?? null)
                !== 'child'
            ) {

                throw ValidationException::withMessages([
                    'variant_groups' =>
                        'Variant tambahan harus menggunakan tipe Variant Tambahan.',
                ]);

            }


            if (
                empty($group['name'])
            ) {

                throw ValidationException::withMessages([
                    'variant_groups' =>
                        'Nama variant tidak boleh kosong.',
                ]);

            }


            if (
                empty($group['values'])
            ) {

                throw ValidationException::withMessages([
                    'variant_groups' =>
                        'Setiap variant harus memiliki minimal satu value.',
                ]);

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected function normalizeVariantAttributes(
        array $attributes,
        array $variantGroups
    ): array {

        $normalized = [];


        $groupCount =
            count($variantGroups);


        for (
            $i = 0;
            $i < $groupCount;
            $i++
        ) {

            $value =
                $attributes[$i]
                ?? '';


            $normalized[] =
                trim(
                    (string) $value
                );

        }


        return $normalized;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE VARIANT SKU
    |--------------------------------------------------------------------------
    */

    protected function generateVariantSku(
        Product $product,
        ?string $requestedSku,
        array $attributes,
        ?int $ignoreVariantId = null
    ): string {

        $requestedSku =
            trim(
                (string) (
                    $requestedSku
                    ?? ''
                )
            );


        /*
        |--------------------------------------------------------------------------
        | GUNAKAN SKU REQUEST
        |--------------------------------------------------------------------------
        */

        if ($requestedSku !== '') {

            $sku =
                $requestedSku;

        }


        /*
        |--------------------------------------------------------------------------
        | AUTO GENERATE
        |--------------------------------------------------------------------------
        */

        else {

            $attributePart =
                collect($attributes)

                    ->map(function ($attribute) {

                        return Str::upper(
                            Str::slug(
                                (string) $attribute
                            )
                        );

                    })

                    ->filter()

                    ->implode('-');


            $sku =
                $product->sku
                . (
                    $attributePart
                        ? '-' . $attributePart
                        : ''
                );

        }


        /*
        |--------------------------------------------------------------------------
        | CEK DUPLICATE SKU
        |--------------------------------------------------------------------------
        */

        $query =
            ProductVariant::query()
                ->where(
                    'sku_variant',
                    $sku
                )
                ->where(
                    'product_id',
                    $product->getKey()
                );


        if (
            $ignoreVariantId !== null
        ) {

            $query->where(
                'id',
                '!=',
                $ignoreVariantId
            );

        }


        if ($query->exists()) {

            $baseSku =
                $sku;


            $counter = 2;


            do {

                $candidate =
                    $baseSku
                    . '-'
                    . $counter;


                $exists =
                    ProductVariant::query()
                        ->where(
                            'sku_variant',
                            $candidate
                        )
                        ->where(
                            'product_id',
                            $product->getKey()
                        )
                        ->when(
                            $ignoreVariantId !== null,
                            function ($q) use (
                                $ignoreVariantId
                            ) {

                                $q->where(
                                    'id',
                                    '!=',
                                    $ignoreVariantId
                                );

                            }
                        )
                        ->exists();


                $counter++;


            } while ($exists);


            $sku =
                $candidate;

        }


        return $sku;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE PRICE
    |--------------------------------------------------------------------------
    */

    protected function normalizePrice(
        $price
    ): int {

        if (
            $price === null
            ||
            $price === ''
        ) {

            return 0;

        }


        return max(
            0,
            (int) $price
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COMPARE ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected function attributesEqual(
        array $first,
        array $second
    ): bool {

        $first =
            array_values(
                array_map(
                    function ($value) {

                        return trim(
                            (string) $value
                        );

                    },
                    $first
                )
            );


        $second =
            array_values(
                array_map(
                    function ($value) {

                        return trim(
                            (string) $value
                        );

                    },
                    $second
                )
            );


        return $first === $second;
    }


    /*
    |--------------------------------------------------------------------------
    | EXTRACT VARIANT IMAGES
    |--------------------------------------------------------------------------
    */

    protected function extractVariantImages(
        array $variantGroups
    ): array {

        $images = [];


        foreach (
            $variantGroups as $group
        ) {

            foreach (
                $group['values']
                ?? [] as $value
            ) {

                if (
                    is_array($value)
                    &&
                    !empty($value['image'])
                ) {

                    $images[] =
                        $value['image'];

                }

            }

        }


        return array_values(
            array_unique(
                array_filter($images)
            )
        );
    }
}
