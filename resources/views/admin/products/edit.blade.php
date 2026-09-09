@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@php

    /*
    |--------------------------------------------------------------------------
    | VARIANT GROUPS DARI DATABASE
    |--------------------------------------------------------------------------
    */

    $variantGroups = $product->variant_groups ?? [];

    if (is_string($variantGroups)) {
        $decoded = json_decode($variantGroups, true);

        $variantGroups = is_array($decoded)
            ? $decoded
            : [];
    }

    if (!is_array($variantGroups)) {
        $variantGroups = [];
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT VARIANTS
    |--------------------------------------------------------------------------
    */

    $productVariants = $product->variants ?? collect();


    /*
    |--------------------------------------------------------------------------
    | NORMALISASI PRODUCT VARIANTS
    |--------------------------------------------------------------------------
    */

    $normalizedVariants = $productVariants->map(function ($variant) {

        $attributes = $variant->attributes ?? [];

        if (is_string($attributes)) {

            $decoded = json_decode($attributes, true);

            $attributes = is_array($decoded)
                ? $decoded
                : [];

        }

        if (!is_array($attributes)) {
            $attributes = [];
        }

        $attributes = array_values(
            array_map(
                fn ($value) => trim((string) $value),
                $attributes
            )
        );

        return [
            'id' => $variant->id,

            'attributes' => $attributes,

            'sku_variant' => $variant->sku_variant ?? '',

            'price' => (int) ($variant->price ?? 0),

            'stock' => (int) ($variant->stock ?? 0),
        ];

    })->values();


    /*
    |--------------------------------------------------------------------------
    | FALLBACK VARIANT GROUPS
    |--------------------------------------------------------------------------
    */

    if (
        empty($variantGroups)
        &&
        $normalizedVariants->isNotEmpty()
    ) {

        $maxAttributeCount = $normalizedVariants
            ->map(fn ($variant) => count($variant['attributes']))
            ->max();

        for (
            $index = 0;
            $index < $maxAttributeCount;
            $index++
        ) {

            $values = $normalizedVariants
                ->map(function ($variant) use ($index) {

                    return $variant['attributes'][$index] ?? null;

                })
                ->filter(function ($value) {

                    return $value !== null
                        && trim((string) $value) !== '';

                })
                ->map(function ($value) {

                    return trim((string) $value);

                })
                ->unique()
                ->values()
                ->map(function ($value) {

                    return [
                        'name' => $value,
                        'image' => null,
                    ];

                })
                ->all();


            if (!empty($values)) {

                $variantGroups[] = [

                    'name' =>
                        $index === 0
                            ? 'Warna'
                            : (
                                $index === 1
                                    ? 'Ukuran'
                                    : 'Variant ' . ($index + 1)
                            ),

                    'type' =>
                        $index === 0
                            ? 'parent'
                            : 'child',

                    'values' => $values,

                ];

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | NORMALISASI VARIANT GROUPS
    |--------------------------------------------------------------------------
    */

    $variantTypesData = collect($variantGroups)
        ->values()
        ->map(function ($group, $groupIndex) {

            $values = collect($group['values'] ?? [])
                ->map(function ($value) {

                    if (is_array($value)) {

                        return [

                            'name' => trim(
                                (string) ($value['name'] ?? '')
                            ),

                            'image' =>
                                !empty($value['image'])
                                    ? $value['image']
                                    : null,

                        ];

                    }

                    return [

                        'name' => trim(
                            (string) $value
                        ),

                        'image' => null,

                    ];

                })
                ->filter(fn ($value) => $value['name'] !== '')
                ->values()
                ->all();


            return [

                'name' => trim(
                    (string) (
                        $group['name']
                        ??
                        (
                            $groupIndex === 0
                                ? 'Warna'
                                : (
                                    $groupIndex === 1
                                        ? 'Ukuran'
                                        : 'Variant ' . ($groupIndex + 1)
                                )
                        )
                    )
                ),

                'type' =>
                    $groupIndex === 0
                        ? 'parent'
                        : 'child',

                'values' => $values,

            ];

        })
        ->filter(function ($group) {

            return !empty($group['name'])
                && !empty($group['values']);

        })
        ->values()
        ->all();


    /*
    |--------------------------------------------------------------------------
    | EXISTING VARIANTS
    |--------------------------------------------------------------------------
    */

    $existingVariantsData = $normalizedVariants->all();

@endphp


<div
    x-data="productEditPage()"
    x-init="init()"
    class="space-y-6 pb-10"
>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Edit Produk
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Gunakan halaman ini untuk memperbaiki informasi produk,
                variant, harga, SKU, atau gambar.
            </p>

        </div>


        <a
            href="{{ route('products.index') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            ← Kembali
        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="rounded-2xl border border-red-200 bg-red-50 p-4">

            <div class="flex gap-3">

                <div class="text-xl text-red-500">
                    ⚠
                </div>

                <div>

                    <h3 class="font-semibold text-red-800">
                        Terdapat kesalahan
                    </h3>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- INFO STOCK --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">

        <div class="flex gap-3">

            <div class="text-xl">
                🔒
            </div>

            <div>

                <h3 class="font-semibold text-amber-900">
                    Stock variant lama tidak dapat diedit
                </h3>

                <p class="mt-1 text-sm leading-6 text-amber-800">

                    Stock variant lama tetap dikunci.

                    Jika Anda menambahkan kombinasi variant baru,
                    stock awal dapat dimasukkan pada kombinasi baru tersebut.

                    Perubahan stock variant lama dilakukan melalui
                    <strong>Stock Opname</strong>.

                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        method="POST"
        action="{{ route('products.update', $product) }}"
        enctype="multipart/form-data"
        @submit="beforeSubmit($event)"
        class="space-y-6"
    >

        @csrf

        @method('PUT')


        {{-- ===================================================== --}}
        {{-- INFORMASI PRODUK --}}
        {{-- ===================================================== --}}

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-5 py-4">

                <h2 class="text-lg font-bold text-gray-900">
                    Informasi Produk
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Perbaiki informasi utama produk.
                </p>

            </div>


            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">


                {{-- NAMA --}}

                <div class="md:col-span-2">

                    <label class="mb-2 block text-sm font-semibold text-gray-700">

                        Nama Produk

                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $product->name) }}"
                        required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                        placeholder="Masukkan nama produk"
                    >

                </div>


                {{-- SKU --}}

                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">

                        SKU Produk

                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="text"
                        name="sku"
                        value="{{ old('sku', $product->sku) }}"
                        required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm uppercase outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                        placeholder="Contoh: HO-001"
                    >

                </div>


                {{-- KATEGORI --}}

                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Kategori
                    </label>

                    <select
                        name="category_id"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                    >

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        @foreach ($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                @selected(
                                    old(
                                        'category_id',
                                        $product->category_id
                                    ) == $category->id
                                )
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- HARGA BELI --}}

                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Harga Beli
                    </label>

                    <input
                        type="number"
                        name="purchase_price"
                        min="0"
                        value="{{ old('purchase_price', $product->purchase_price) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                    >

                </div>


                {{-- HARGA JUAL --}}

                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Harga Jual
                    </label>

                    <input
                        type="number"
                        name="price"
                        min="0"
                        value="{{ old('price', $product->price) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                    >

                </div>


                {{-- UNIT --}}

                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Satuan
                    </label>

                    <input
                        type="text"
                        name="unit"
                        value="{{ old('unit', $product->unit ?? 'pcs') }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                        placeholder="pcs"
                    >

                </div>


                {{-- MINIMUM STOCK --}}

                <div>

                    <label class="mb-2 block text-sm font-semibold text-gray-700">
                        Minimum Stock
                    </label>

                    <input
                        type="number"
                        name="min_stock"
                        min="0"
                        value="{{ old('min_stock', $product->min_stock ?? 0) }}"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                    >

                    <p class="mt-1 text-xs text-gray-500">
                        Digunakan untuk indikator stock menipis.
                    </p>

                </div>


                {{-- STATUS --}}

                <div class="md:col-span-2">

                    <label class="flex cursor-pointer items-center gap-3">

                        <input
                            type="hidden"
                            name="is_active"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(
                                old(
                                    'is_active',
                                    $product->is_active
                                )
                            )
                            class="h-5 w-5 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                        >

                        <span>

                            <span class="block text-sm font-semibold text-gray-800">
                                Produk Aktif
                            </span>

                            <span class="block text-xs text-gray-500">
                                Produk dapat digunakan di sistem kasir.
                            </span>

                        </span>

                    </label>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- GAMBAR PRODUK --}}
        {{-- ===================================================== --}}

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-5 py-4">

                <h2 class="text-lg font-bold text-gray-900">
                    Gambar Produk
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ganti gambar utama produk jika diperlukan.
                </p>

            </div>


            <div class="p-5">

                <div class="flex flex-col gap-5 sm:flex-row sm:items-start">

                    @if ($product->image)

                        <div>

                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Gambar Saat Ini
                            </p>

                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="h-32 w-32 rounded-2xl border border-gray-200 object-cover"
                            >

                        </div>

                    @endif


                    <div class="flex-1">

                        <label class="mb-2 block text-sm font-semibold text-gray-700">
                            Upload Gambar Baru
                        </label>

                        <input
                            type="file"
                            name="image"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            @change="previewMainImage($event)"
                            class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-sm"
                        >

                        <p class="mt-2 text-xs text-gray-500">
                            JPG, JPEG, PNG, WEBP. Maksimal 5MB.
                        </p>


                        <template x-if="mainImagePreview">

                            <div class="mt-4">

                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Preview Gambar Baru
                                </p>

                                <img
                                    :src="mainImagePreview"
                                    class="h-32 w-32 rounded-2xl border border-gray-200 object-cover"
                                    alt="Preview"
                                >

                            </div>

                        </template>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- VARIANT PRODUK --}}
        {{-- ===================================================== --}}

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-6 py-5">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-700"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>

                        </div>

                        <div>

                            <h2 class="text-lg font-bold text-gray-900">
                                Variant Produk
                            </h2>

                            <p class="text-sm text-gray-500">
                                Tambahkan warna, ukuran, atau variant lainnya.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        @click="addVariantType()"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>

                        Tambah Variant

                    </button>

                </div>

            </div>


            <div class="p-6">

                <div
                    x-show="variantTypes.length === 0"
                    class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center"
                >

                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-white">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M12 6v12m6-6H6"
                            />
                        </svg>

                    </div>

                    <h3 class="font-semibold text-gray-800">
                        Belum ada variant
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Klik "Tambah Variant" untuk menambahkan variant produk.
                    </p>

                </div>


                <div class="space-y-5">

                    <template
                        x-for="(variant, index) in variantTypes"
                        :key="variant.uid"
                    >

                        <div class="overflow-hidden rounded-2xl border border-gray-200">

                            {{-- HEADER VARIANT --}}

                            <div class="border-b border-gray-200 bg-gray-50 px-5 py-4">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-900 text-sm font-bold text-white"
                                            x-text="index + 1"
                                        ></div>

                                        <div>

                                            <h3
                                                class="font-bold text-gray-900"
                                                x-text="'Variant ' + (index + 1)"
                                            ></h3>

                                            <p
                                                class="text-xs text-gray-500"
                                                x-show="index === 0"
                                            >
                                                Variant utama, contoh: Warna
                                            </p>

                                            <p
                                                class="text-xs text-gray-500"
                                                x-show="index > 0"
                                            >
                                                Variant tambahan, contoh: Ukuran,
                                                Bahan, Model
                                            </p>

                                        </div>

                                    </div>


                                    <button
                                        type="button"
                                        @click="removeVariantType(index)"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50"
                                        title="Hapus variant"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a1.995 1.995 0 01-1.995-1.858L5 7m5 4v6m4-6V7a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8"
                                            />
                                        </svg>

                                    </button>

                                </div>

                            </div>


                            <div class="space-y-5 p-5">


                                {{-- NAMA VARIANT --}}

                                <div>

                                    <label class="mb-2 block text-sm font-semibold text-gray-700">

                                        Nama Variant

                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input
                                        type="text"
                                        :name="'variant_groups[' + index + '][name]'"
                                        x-model="variant.name"
                                        @input="regenerateCombinations()"
                                        :placeholder="index === 0 ? 'Contoh: Warna' : 'Contoh: Ukuran'"
                                        required
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                                    >

                                    <input
                                        type="hidden"
                                        :name="'variant_groups[' + index + '][type]'"
                                        :value="index === 0 ? 'parent' : 'child'"
                                    >

                                </div>


                                {{-- NILAI VARIANT --}}

                                <div>

                                    <div class="mb-3 flex items-center justify-between gap-3">

                                        <div>

                                            <label class="block text-sm font-semibold text-gray-700">
                                                Nilai Variant
                                            </label>

                                            <p class="mt-1 text-xs text-gray-500">
                                                Tambahkan nilai yang tersedia.
                                            </p>

                                        </div>


                                        <button
                                            type="button"
                                            @click="addValue(index)"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 4v16m8-8H4"
                                                />
                                            </svg>

                                            Tambah Nilai

                                        </button>

                                    </div>


                                    <div class="space-y-3">

                                        <template
                                            x-for="(value, valueIndex) in variant.values"
                                            :key="value.uid"
                                        >

                                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                                                <div class="flex flex-col gap-4 lg:flex-row">


                                                    {{-- VALUE --}}

                                                    <div class="flex-1">

                                                        <label class="mb-2 block text-xs font-semibold text-gray-600">
                                                            Nilai
                                                        </label>

                                                        <input
                                                            type="text"
                                                            :name="'variant_groups[' + index + '][values][' + valueIndex + '][name]'"
                                                            x-model="value.name"
                                                            @input="regenerateCombinations()"
                                                            :placeholder="index === 0 ? 'Contoh: Hitam' : 'Contoh: M'"
                                                            required
                                                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                                                        >

                                                    </div>


                                                    {{-- GAMBAR VALUE UTAMA --}}

                                                    <div
                                                        class="w-full lg:w-80"
                                                        x-show="index === 0"
                                                    >

                                                        <label class="mb-2 block text-xs font-semibold text-gray-600">
                                                            Gambar
                                                        </label>

                                                        <div class="flex items-center gap-3">

                                                            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-white">

                                                                <template x-if="value.preview || value.image">

                                                                    <img
                                                                        :src="value.preview || ('{{ asset('storage') }}/' + value.image)"
                                                                        class="h-full w-full object-cover"
                                                                        alt=""
                                                                    >

                                                                </template>


                                                                <template x-if="!value.preview && !value.image">

                                                                    <svg
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        class="h-6 w-6 text-gray-400"
                                                                        fill="none"
                                                                        viewBox="0 0 24 24"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            stroke-width="1.5"
                                                                            d="M4 16l4-4a3 3 0 014 0l4 4m-1-5h.01M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v14a1 1 0 001 1z"
                                                                        />
                                                                    </svg>

                                                                </template>

                                                            </div>


                                                            <input
                                                                type="file"
                                                                accept="image/*"
                                                                :name="'variant_groups[' + index + '][values][' + valueIndex + '][image]'"
                                                                @change="previewVariantImage($event, index, valueIndex)"
                                                                class="block w-full text-xs text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-gray-800"
                                                            >

                                                        </div>


                                                        <p class="mt-1.5 text-[11px] text-gray-500">
                                                            Kosongkan jika ingin mempertahankan gambar lama.
                                                            Gambar ini digunakan untuk nilai variant utama,
                                                            misalnya foto warna Hitam.
                                                        </p>

                                                    </div>


                                                    {{-- EXISTING IMAGE --}}

                                                    <input
                                                        type="hidden"
                                                        :name="'variant_groups[' + index + '][values][' + valueIndex + '][existing_image]'"
                                                        :value="value.image || ''"
                                                    >


                                                    {{-- DELETE VALUE --}}

                                                    <div class="flex items-end">

                                                        <button
                                                            type="button"
                                                            @click="removeValue(index, valueIndex)"
                                                            class="flex h-10 w-full items-center justify-center rounded-xl border border-red-200 text-red-500 transition hover:bg-red-50 lg:w-10"
                                                            title="Hapus nilai"
                                                        >

                                                            <svg
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                stroke="currentColor"
                                                            >
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a1.995 1.995 0 01-1.995-1.858L5 7m5 4v6m4-6V7a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8"
                                                                />
                                                            </svg>

                                                        </button>

                                                    </div>

                                                </div>

                                            </div>

                                        </template>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </template>

                </div>


                {{-- TIPS --}}

                <div
                    x-show="variantTypes.length > 0"
                    class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4"
                >

                    <div class="flex gap-3">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mt-0.5 h-5 w-5 shrink-0 text-gray-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"
                            />
                        </svg>

                        <div class="text-sm text-gray-600">

                            <p class="font-semibold text-gray-800">
                                Tips Variant
                            </p>

                            <p class="mt-1">
                                Variant pertama biasanya digunakan untuk
                                <strong>Warna</strong>.
                                Jika ada variant kedua, gunakan
                                <strong>Ukuran</strong>.
                                Variant ketiga dan seterusnya dapat digunakan
                                untuk kebutuhan lain.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- KOMBINASI --}}
        {{-- ===================================================== --}}

        <div
            x-show="combinations.length > 0"
            x-cloak
            class="rounded-2xl border border-gray-200 bg-white shadow-sm"
        >

            <div class="border-b border-gray-100 px-5 py-4">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="text-lg font-bold text-gray-900">
                            Kombinasi Variant
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Stock variant lama hanya ditampilkan sebagai informasi.
                            Variant baru dapat diisi stock awal.
                        </p>

                    </div>


                    <div class="rounded-xl bg-gray-100 px-4 py-2 text-right">

                        <div class="text-xs text-gray-500">
                            Total Stock
                        </div>

                        <div
                            class="text-lg font-bold text-gray-900"
                            x-text="formatNumber(totalStock)"
                        ></div>

                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-600">
                                #
                            </th>


                            <template
                                x-for="(variantType, typeIndex) in variantTypes"
                                :key="'head-' + variantType.uid"
                            >

                                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-600">

                                    <span
                                        x-text="
                                            variantType.name ||
                                            ('Variant ' + (typeIndex + 1))
                                        "
                                    ></span>

                                </th>

                            </template>


                            <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-600">
                                SKU Variant
                            </th>


                            <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-600">
                                Harga
                            </th>


                            <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-600">
                                Stock
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        <template
                            x-for="(combination, combinationIndex) in combinations"
                            :key="combination.uid"
                        >

                            <tr class="hover:bg-gray-50">

                                {{-- INDEX --}}

                                <td class="px-4 py-3 font-semibold text-gray-500">

                                    <span
                                        x-text="combinationIndex + 1"
                                    ></span>

                                </td>


                                {{-- ATTRIBUTES --}}

                                <template
                                    x-for="(attribute, attributeIndex) in combination.attributes"
                                    :key="'attribute-' + combination.uid + '-' + attributeIndex"
                                >

                                    <td class="px-4 py-3">

                                        <div class="font-medium text-gray-800">

                                            <span x-text="attribute"></span>

                                        </div>

                                    </td>

                                </template>


                                {{-- SKU --}}

                                <td class="px-4 py-3">

                                    <input
                                        type="text"
                                        :name="`variants[${combinationIndex}][sku_variant]`"
                                        x-model="combination.sku_variant"
                                        class="w-48 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium outline-none focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                                        placeholder="SKU Variant"
                                    >

                                </td>


                                {{-- PRICE --}}

                                <td class="px-4 py-3">

                                    <input
                                        type="number"
                                        :name="`variants[${combinationIndex}][price]`"
                                        x-model="combination.price"
                                        min="0"
                                        class="w-36 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs outline-none focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                                    >

                                </td>


                                {{-- STOCK --}}

                                <td class="px-4 py-3">

                                    {{-- ID VARIANT --}}

                                    <input
                                        type="hidden"
                                        :name="`variants[${combinationIndex}][id]`"
                                        :value="combination.id || ''"
                                    >


                                    {{-- ATTRIBUTES --}}

                                    <template
                                        x-for="(attribute, attributeIndex) in combination.attributes"
                                        :key="'hidden-attribute-' + combination.uid + '-' + attributeIndex"
                                    >

                                        <input
                                            type="hidden"
                                            :name="`variants[${combinationIndex}][attributes][${attributeIndex}]`"
                                            :value="attribute"
                                        >

                                    </template>


                                    {{-- STOCK LAMA --}}

                                    <template x-if="combination.id">

                                        <div class="flex items-center gap-2">

                                            <div
                                                class="min-w-24 rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700"
                                                x-text="formatNumber(combination.stock)"
                                            ></div>

                                            <span class="text-xs text-gray-400">
                                                {{ $product->unit ?? 'pcs' }}
                                            </span>

                                        </div>

                                    </template>


                                    {{-- STOCK BARU --}}

                                    <template x-if="!combination.id">

                                        <div class="flex items-center gap-2">

                                            <input
                                                type="number"
                                                min="0"
                                                step="1"
                                                :name="`variants[${combinationIndex}][stock]`"
                                                x-model.number="combination.stock"
                                                @input="calculateTotalStock()"
                                                class="w-28 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-800 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                                                placeholder="Stock"
                                            >

                                            <span class="text-xs text-gray-400">
                                                {{ $product->unit ?? 'pcs' }}
                                            </span>

                                        </div>

                                    </template>

                                </td>

                            </tr>

                        </template>

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- NO COMBINATION --}}
        {{-- ===================================================== --}}

        <div
            x-show="variantTypes.length > 0 && combinations.length === 0"
            x-cloak
            class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center"
        >

            <div class="text-3xl">
                ⚠️
            </div>

            <h3 class="mt-3 font-semibold text-gray-800">
                Kombinasi variant belum tersedia
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Pastikan setiap variant memiliki minimal satu value.
            </p>

        </div>


        {{-- ===================================================== --}}
        {{-- ACTION --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

            <a
                href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
            >
                Batal
            </a>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="submitting"
            >

                <template x-if="!submitting">

                    <span>
                        Simpan Perubahan
                    </span>

                </template>


                <template x-if="submitting">

                    <span>
                        Menyimpan...
                    </span>

                </template>

            </button>

        </div>

    </form>

</div>


<script>

function productEditPage() {

    return {

        /*
        |--------------------------------------------------------------------------
        | STATE
        |--------------------------------------------------------------------------
        */

        variantTypes: [],

        combinations: [],

        existingVariants: [],

        mainImagePreview: null,

        totalStock: 0,

        submitting: false,


        /*
        |--------------------------------------------------------------------------
        | INIT
        |--------------------------------------------------------------------------
        */

        init() {

            const initialVariantTypes =
                @json($variantTypesData);


            const existingVariants =
                @json($existingVariantsData);


            /*
            |--------------------------------------------------------------------------
            | EXISTING VARIANTS
            |--------------------------------------------------------------------------
            */

            this.existingVariants =
                Array.isArray(existingVariants)
                    ? existingVariants.map(variant => {

                        return {

                            id:
                                variant.id ?? null,

                            attributes:
                                Array.isArray(variant.attributes)
                                    ? variant.attributes.map(
                                        value =>
                                            String(value ?? '').trim()
                                    )
                                    : [],

                            sku_variant:
                                variant.sku_variant ?? '',

                            price:
                                variant.price ?? '',

                            stock:
                                Number(
                                    variant.stock ?? 0
                                ),

                        };

                    })
                    : [];


            /*
            |--------------------------------------------------------------------------
            | VARIANT TYPES
            |--------------------------------------------------------------------------
            |
            | SETIAP VALUE WAJIB MEMILIKI UID.
            |
            | UID INI TIDAK BOLEH BERUBAH SAAT VALUE DIGANTI NAMANYA.
            |
            */

            this.variantTypes =
                Array.isArray(initialVariantTypes)

                    ? initialVariantTypes.map(
                        (variantType, typeIndex) => {

                            return {

                                uid:
                                    this.generateUid('type'),


                                name:
                                    String(
                                        variantType.name || ''
                                    ).trim()
                                    ||
                                    (
                                        typeIndex === 0
                                            ? 'Warna'
                                            : (
                                                typeIndex === 1
                                                    ? 'Ukuran'
                                                    : 'Variant ' + (
                                                        typeIndex + 1
                                                    )
                                            )
                                    ),


                                type:
                                    typeIndex === 0
                                        ? 'parent'
                                        : 'child',


                                values:
                                    Array.isArray(
                                        variantType.values
                                    )

                                        ? variantType.values.map(
                                            value => {

                                                return {

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | UID STABIL
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    uid:
                                                        value.uid
                                                        ||
                                                        this.generateUid(
                                                            'value'
                                                        ),

                                                    name:
                                                        String(
                                                            value.name
                                                            ?? ''
                                                        ).trim(),

                                                    image:
                                                        value.image
                                                        || null,

                                                    preview:
                                                        null,

                                                };

                                            }
                                        )

                                        : [],

                            };

                        }
                    )

                    : [];


            /*
            |--------------------------------------------------------------------------
            | GENERATE KOMBINASI AWAL
            |--------------------------------------------------------------------------
            */

            this.regenerateCombinations();

        },


        /*
        |--------------------------------------------------------------------------
        | GENERATE UID
        |--------------------------------------------------------------------------
        */

        generateUid(prefix = 'item') {

            return (
                prefix
                + '_'
                + Date.now()
                + '_'
                + Math.random()
                    .toString(36)
                    .substring(2, 10)
            );

        },


        /*
        |--------------------------------------------------------------------------
        | TAMBAH VARIANT TYPE
        |--------------------------------------------------------------------------
        */

        addVariantType() {

            const index =
                this.variantTypes.length;


            this.variantTypes.push({

                uid:
                    this.generateUid('type'),

                name:
                    index === 0
                        ? 'Warna'
                        : (
                            index === 1
                                ? 'Ukuran'
                                : 'Variant ' + (
                                    index + 1
                                )
                        ),

                type:
                    index === 0
                        ? 'parent'
                        : 'child',

                values: [

                    {

                        uid:
                            this.generateUid('value'),

                        name:
                            '',

                        image:
                            null,

                        preview:
                            null,

                    }

                ],

            });


            this.regenerateCombinations();

        },


        /*
        |--------------------------------------------------------------------------
        | HAPUS VARIANT TYPE
        |--------------------------------------------------------------------------
        */

        removeVariantType(index) {

            if (
                !confirm(
                    'Yakin ingin menghapus variant ini?'
                )
            ) {
                return;
            }


            this.variantTypes.splice(
                index,
                1
            );


            this.variantTypes.forEach(
                (variantType, currentIndex) => {

                    variantType.type =
                        currentIndex === 0
                            ? 'parent'
                            : 'child';

                }
            );


            this.regenerateCombinations();

        },


        /*
        |--------------------------------------------------------------------------
        | TAMBAH VALUE
        |--------------------------------------------------------------------------
        */

        addValue(typeIndex) {

            const variantType =
                this.variantTypes[typeIndex];


            if (!variantType) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | VALUE BARU WAJIB UID BARU
            |--------------------------------------------------------------------------
            */

            variantType.values.push({

                uid:
                    this.generateUid('value'),

                name:
                    '',

                image:
                    null,

                preview:
                    null,

            });


            this.regenerateCombinations();

        },


        /*
        |--------------------------------------------------------------------------
        | HAPUS VALUE
        |--------------------------------------------------------------------------
        */

        removeValue(
            typeIndex,
            valueIndex
        ) {

            const variantType =
                this.variantTypes[typeIndex];


            if (!variantType) {
                return;
            }


            if (
                variantType.values.length <= 1
            ) {

                alert(
                    'Setiap variant harus memiliki minimal satu value.'
                );

                return;

            }


            variantType.values.splice(
                valueIndex,
                1
            );


            this.regenerateCombinations();

        },


        /*
        |--------------------------------------------------------------------------
        | PREVIEW GAMBAR PRODUK
        |--------------------------------------------------------------------------
        */

        previewMainImage(event) {

            const file =
                event.target.files?.[0];


            if (!file) {

                this.mainImagePreview =
                    null;

                return;

            }


            const allowedTypes = [

                'image/jpeg',

                'image/png',

                'image/jpg',

                'image/webp',

            ];


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                alert(
                    'Format gambar harus JPG, JPEG, PNG, atau WEBP.'
                );


                event.target.value =
                    '';


                this.mainImagePreview =
                    null;


                return;

            }


            if (
                file.size >
                5 * 1024 * 1024
            ) {

                alert(
                    'Ukuran gambar maksimal 5MB.'
                );


                event.target.value =
                    '';


                this.mainImagePreview =
                    null;


                return;

            }


            this.mainImagePreview =
                URL.createObjectURL(file);

        },


        /*
        |--------------------------------------------------------------------------
        | PREVIEW GAMBAR VARIANT
        |--------------------------------------------------------------------------
        */

        previewVariantImage(
            event,
            typeIndex,
            valueIndex
        ) {

            const file =
                event.target.files?.[0];


            const variantType =
                this.variantTypes[typeIndex];


            if (
                !file
                ||
                !variantType
                ||
                !variantType.values[valueIndex]
            ) {

                return;

            }


            const allowedTypes = [

                'image/jpeg',

                'image/png',

                'image/jpg',

                'image/webp',

            ];


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                alert(
                    'Format gambar harus JPG, JPEG, PNG, atau WEBP.'
                );


                event.target.value =
                    '';


                return;

            }


            if (
                file.size >
                5 * 1024 * 1024
            ) {

                alert(
                    'Ukuran gambar maksimal 5MB.'
                );


                event.target.value =
                    '';


                return;

            }


            variantType
                .values[valueIndex]
                .preview =
                    URL.createObjectURL(file);

        },


        /*
        |--------------------------------------------------------------------------
        | GENERATE KOMBINASI
        |--------------------------------------------------------------------------
        |
        | LOGIC UTAMA:
        |
        | 1. Value lama mempunyai UID.
        | 2. Value baru mempunyai UID baru.
        | 3. Kombinasi dicocokkan berdasarkan UID value.
        | 4. TIDAK BOLEH MENCARI KOMBINASI BERDASARKAN INDEX.
        | 5. Kombinasi baru selalu id = null.
        |
        */

        regenerateCombinations() {

            const previousCombinations =
                Array.isArray(this.combinations)
                    ? [...this.combinations]
                    : [];


            /*
            |--------------------------------------------------------------------------
            | GROUP VALID
            |--------------------------------------------------------------------------
            */

            const validGroups =
                this.variantTypes

                    .map(variantType => {

                        return {

                            name:
                                String(
                                    variantType.name || ''
                                ).trim(),

                            values:
                                Array.isArray(
                                    variantType.values
                                )

                                    ? variantType.values

                                        .map(value => {

                                            return {

                                                uid:
                                                    value.uid,

                                                name:
                                                    String(
                                                        value.name || ''
                                                    ).trim(),

                                            };

                                        })

                                        .filter(
                                            value =>
                                                value.name !== ''
                                        )

                                    : [],

                        };

                    })

                    .filter(group => {

                        return (
                            group.name !== ''
                            &&
                            group.values.length > 0
                        );

                    });


            /*
            |--------------------------------------------------------------------------
            | TIDAK ADA VARIANT
            |--------------------------------------------------------------------------
            */

            if (
                validGroups.length === 0
            ) {

                this.combinations =
                    [];

                this.calculateTotalStock();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | CARTESIAN PRODUCT
            |--------------------------------------------------------------------------
            */

            let result = [
                []
            ];


            validGroups.forEach(
                group => {

                    const next = [];


                    result.forEach(
                        existing => {

                            group.values.forEach(
                                value => {

                                    next.push([
                                        ...existing,
                                        value
                                    ]);

                                }
                            );

                        }
                    );


                    result =
                        next;

                }
            );


            /*
            |--------------------------------------------------------------------------
            | BENTUK KOMBINASI
            |--------------------------------------------------------------------------
            */

            const newCombinations =
                result.map(
                    selectedValues => {

                        const attributes =
                            selectedValues.map(
                                value =>
                                    String(
                                        value.name
                                    ).trim()
                            );


                        const valueUids =
                            selectedValues.map(
                                value =>
                                    value.uid
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | CARI DATABASE BERDASARKAN ATTRIBUTE
                        |--------------------------------------------------------------------------
                        */

                        const existingFromDb =
                            this.findExistingVariant(
                                attributes
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | CARI KOMBINASI SEBELUMNYA
                        |--------------------------------------------------------------------------
                        |
                        | BUKAN BERDASARKAN INDEX.
                        |
                        | INI YANG MEMBUAT VALUE BARU TIDAK MENGAMBIL ID LAMA.
                        |
                        */

                        const previous =
                            previousCombinations.find(
                                combination => {

                                    const previousUids =
                                        Array.isArray(
                                            combination.valueUids
                                        )
                                            ? combination.valueUids
                                            : [];


                                    if (
                                        previousUids.length
                                        !==
                                        valueUids.length
                                    ) {

                                        return false;

                                    }


                                    return previousUids.every(
                                        (
                                            uid,
                                            index
                                        ) => {

                                            return (
                                                uid
                                                ===
                                                valueUids[index]
                                            );

                                        }
                                    );

                                }
                            )
                            ||
                            null;


                        /*
                        |--------------------------------------------------------------------------
                        | JIKA DATABASE TIDAK KETEMU,
                        | GUNAKAN ID KOMBINASI LAMA YANG SAMA UID-NYA
                        |--------------------------------------------------------------------------
                        */

                        const existingByPreviousId =
                            !existingFromDb
                            &&
                            previous
                            &&
                            previous.id

                                ? (
                                    this.existingVariants.find(
                                        variant => {

                                            return (
                                                Number(
                                                    variant.id
                                                )
                                                ===
                                                Number(
                                                    previous.id
                                                )
                                            );

                                        }
                                    )
                                    ||
                                    null
                                )

                                : null;


                        const existing =
                            existingFromDb
                            ||
                            existingByPreviousId;


                        /*
                        |--------------------------------------------------------------------------
                        | RETURN
                        |--------------------------------------------------------------------------
                        */

                        return {

                            uid:
                                this.generateUid(
                                    'combination'
                                ),


                            id:
                                existing
                                    ? existing.id
                                    : (
                                        previous?.id
                                        ??
                                        null
                                    ),


                            valueUids:
                                valueUids,


                            attributes:
                                attributes,


                            sku_variant:
                                existing?.sku_variant
                                ||
                                previous?.sku_variant
                                ||
                                this.generateSku(
                                    attributes
                                ),


                            price:
                                existing?.price
                                ??
                                previous?.price
                                ??
                                '',


                            stock:
                                existing
                                    ? Number(
                                        existing.stock || 0
                                    )
                                    : Number(
                                        previous?.stock || 0
                                    ),

                        };

                    }
                );


            this.combinations =
                newCombinations;


            this.calculateTotalStock();

        },


        /*
        |--------------------------------------------------------------------------
        | CARI EXISTING VARIANT
        |--------------------------------------------------------------------------
        */

        findExistingVariant(attributes) {

            const target =
                attributes.map(
                    value =>
                        String(
                            value
                        ).trim()
                );


            return this.existingVariants.find(
                variant => {

                    const variantAttributes =
                        Array.isArray(
                            variant.attributes
                        )

                            ? variant.attributes.map(
                                value =>
                                    String(
                                        value
                                    ).trim()
                            )

                            : [];


                    if (
                        variantAttributes.length
                        !==
                        target.length
                    ) {

                        return false;

                    }


                    return variantAttributes.every(
                        (
                            value,
                            index
                        ) => {

                            return (
                                value
                                ===
                                target[index]
                            );

                        }
                    );

                }
            )
            ||
            null;

        },


        /*
        |--------------------------------------------------------------------------
        | GENERATE SKU
        |--------------------------------------------------------------------------
        */

        generateSku(attributes) {

            const productSku =
                @json($product->sku);


            const attributePart =
                attributes

                    .map(value => {

                        return String(
                            value
                        )
                            .trim()
                            .toUpperCase()
                            .replace(
                                /[^A-Z0-9]+/g,
                                '-'
                            )
                            .replace(
                                /^-+|-+$/g,
                                ''
                            );

                    })

                    .filter(Boolean)

                    .join('-');


            if (
                !attributePart
            ) {

                return productSku;

            }


            return (
                productSku
                + '-'
                + attributePart
            );

        },


        /*
        |--------------------------------------------------------------------------
        | TOTAL STOCK
        |--------------------------------------------------------------------------
        */

        calculateTotalStock() {

            this.totalStock =
                this.combinations.reduce(
                    (
                        total,
                        combination
                    ) => {

                        return (
                            total
                            +
                            Number(
                                combination.stock
                                ||
                                0
                            )
                        );

                    },
                    0
                );

        },


        /*
        |--------------------------------------------------------------------------
        | FORMAT NUMBER
        |--------------------------------------------------------------------------
        */

        formatNumber(value) {

            return Number(
                value || 0
            ).toLocaleString(
                'id-ID'
            );

        },


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SUBMIT
        |--------------------------------------------------------------------------
        */

        beforeSubmit(event) {

            if (
                this.submitting
            ) {

                event.preventDefault();

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI VARIANT
            |--------------------------------------------------------------------------
            */

            for (
                let typeIndex = 0;
                typeIndex < this.variantTypes.length;
                typeIndex++
            ) {

                const variantType =
                    this.variantTypes[typeIndex];


                if (
                    !String(
                        variantType.name || ''
                    ).trim()
                ) {

                    event.preventDefault();


                    alert(
                        `Nama Variant ${typeIndex + 1} wajib diisi.`
                    );


                    return;

                }


                if (
                    !Array.isArray(
                        variantType.values
                    )
                    ||
                    variantType.values.length === 0
                ) {

                    event.preventDefault();


                    alert(
                        `Variant ${variantType.name} harus memiliki minimal satu value.`
                    );


                    return;

                }


                for (
                    let valueIndex = 0;
                    valueIndex < variantType.values.length;
                    valueIndex++
                ) {

                    const value =
                        variantType.values[valueIndex];


                    if (
                        !String(
                            value.name || ''
                        ).trim()
                    ) {

                        event.preventDefault();


                        alert(
                            `Value pada variant ${variantType.name} tidak boleh kosong.`
                        );


                        return;

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI KOMBINASI
            |--------------------------------------------------------------------------
            */

            if (
                this.variantTypes.length > 0
                &&
                this.combinations.length === 0
            ) {

                event.preventDefault();


                alert(
                    'Kombinasi variant belum tersedia.'
                );


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            this.submitting =
                true;

        }

    };

}

</script>

@endsection
