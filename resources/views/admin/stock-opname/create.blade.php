@extends('layouts.app')

@section('title', 'Stock Opname Baru')

@section('content')

<div class="max-w-7xl mx-auto p-4">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex items-center gap-2 mb-1">

        <a
            href="{{ route('admin.stock-opname.index') }}"
            class="text-gray-400 hover:text-amber-600 shrink-0"
            title="Kembali"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L8.414 10l4.293 4.293z"
                    clip-rule="evenodd"
                />
            </svg>
        </a>

        <h1 class="text-lg font-semibold text-gray-800">
            Stock Opname Baru
        </h1>

    </div>

    <p class="text-sm text-gray-500 mb-6 ml-7">
        Masukkan jumlah stok fisik berdasarkan hasil pengecekan warehouse.
    </p>


    {{-- ========================================================= --}}
    {{-- ERROR --}}
    {{-- ========================================================= --}}

    @if ($errors->any())

        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-sm">

            <p class="font-medium mb-1">
                Periksa kembali data yang dimasukkan.
            </p>

            <ul class="list-disc list-inside text-xs space-y-1">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        action="{{ route('admin.stock-opname.store') }}"
        method="POST"
        id="stock-opname-form"
    >

        @csrf


        {{-- ===================================================== --}}
        {{-- INFORMASI OPNAME --}}
        {{-- ===================================================== --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">

            <h2 class="font-semibold text-gray-800 mb-4">
                Informasi Opname
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- TANGGAL --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Opname
                    </label>

                    <input
                        type="date"
                        name="opname_date"
                        value="{{ old('opname_date', now()->format('Y-m-d')) }}"
                        required
                        class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 border-gray-300"
                    >

                </div>


                {{-- CATATAN --}}

                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">

                        Catatan

                        <span class="text-gray-400 font-normal">
                            (opsional)
                        </span>

                    </label>

                    <input
                        type="text"
                        name="note"
                        value="{{ old('note') }}"
                        placeholder="Contoh: pengecekan stok bulanan"
                        class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 border-gray-300"
                    >

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- INFO --}}
        {{-- ===================================================== --}}

        <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 p-4">

            <div class="flex gap-3">

                <div class="shrink-0 text-blue-600">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 22a10 10 0 100-20 10 10 0 000 20z"
                        />
                    </svg>
                </div>

                <div>

                    <p class="text-sm font-semibold text-blue-800">
                        Cara kerja Stock Opname
                    </p>

                    <p class="mt-1 text-xs leading-5 text-blue-700">
                        Stok Sistem diambil dari database dan tidak dapat diubah.
                        Masukkan hasil penghitungan fisik pada kolom
                        <strong>Stok Fisik</strong>.
                        Selisih akan dihitung otomatis.
                    </p>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- PEMERIKSAAN STOK --}}
        {{-- ===================================================== --}}

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- HEADER --}}

            <div class="p-6 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>

                        <h2 class="font-semibold text-gray-800">
                            Pemeriksaan Stok
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Klik produk untuk melihat seluruh kombinasi variant.
                        </p>

                    </div>


                    {{-- COUNTER --}}

                    <div class="text-sm text-gray-600">

                        Selisih ditemukan:

                        <span
                            id="diff-count"
                            class="font-semibold text-gray-800"
                        >
                            0
                        </span>

                    </div>

                </div>


                {{-- SEARCH --}}

                <div class="mt-5">

                    <div class="relative">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="absolute left-3 top-3 w-5 h-5 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0010.6 10.6Z"
                            />
                        </svg>

                        <input
                            type="text"
                            id="product-search"
                            placeholder="Cari nama produk atau SKU..."
                            autocomplete="off"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        >

                    </div>

                    <div class="flex items-center justify-between mt-2">

                        <p class="text-xs text-gray-400">
                            Klik produk untuk membuka pemeriksaan stok.
                        </p>

                        <p class="text-xs text-gray-500">

                            <span id="product-count">
                                {{ $products->count() }}
                            </span>

                            produk

                        </p>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- PRODUCT LIST --}}
            {{-- ================================================= --}}

            <div
                id="product-list"
                class="divide-y divide-gray-100"
            >

                @php
                    $index = 0;
                @endphp


                @forelse ($products as $product)

                    @php

                        $variants = $product->variants ?? collect();

                        /*
                        |--------------------------------------------------------------------------
                        | VARIANT GROUPS
                        |--------------------------------------------------------------------------
                        */

                        $variantGroups = $product->variant_groups ?? [];

                        if (is_string($variantGroups)) {

                            $decoded = json_decode(
                                $variantGroups,
                                true
                            );

                            $variantGroups =
                                is_array($decoded)
                                    ? $decoded
                                    : [];

                        }

                        if (!is_array($variantGroups)) {
                            $variantGroups = [];
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | FALLBACK GROUP
                        |--------------------------------------------------------------------------
                        |
                        | Jika variant_groups kosong tetapi attributes tersedia,
                        | bentuk otomatis.
                        |
                        */

                        if (
                            empty($variantGroups)
                            &&
                            $variants->count()
                        ) {

                            $allAttributes = $variants
                                ->map(function ($variant) {

                                    $attributes =
                                        $variant->attributes ?? [];

                                    if (is_string($attributes)) {

                                        $decoded =
                                            json_decode(
                                                $attributes,
                                                true
                                            );

                                        $attributes =
                                            is_array($decoded)
                                                ? $decoded
                                                : [];

                                    }

                                    return is_array($attributes)
                                        ? array_values($attributes)
                                        : [];

                                })
                                ->filter(
                                    fn ($attributes) =>
                                        !empty($attributes)
                                );


                            $attributeCount =
                                $allAttributes
                                    ->map(
                                        fn ($attributes) =>
                                            count($attributes)
                                    )
                                    ->max();


                            for (
                                $i = 0;
                                $i < $attributeCount;
                                $i++
                            ) {

                                $values =
                                    $allAttributes
                                        ->map(
                                            fn ($attributes) =>
                                                $attributes[$i] ?? null
                                        )
                                        ->filter(
                                            fn ($value) =>
                                                $value !== null
                                                &&
                                                trim((string) $value) !== ''
                                        )
                                        ->map(
                                            fn ($value) =>
                                                trim((string) $value)
                                        )
                                        ->unique()
                                        ->values()
                                        ->all();


                                if (!empty($values)) {

                                    $variantGroups[] = [

                                        'name' =>
                                            $i === 0
                                                ? 'Warna'
                                                : (
                                                    $i === 1
                                                        ? 'Ukuran'
                                                        : 'Variant ' . ($i + 1)
                                                ),

                                        'values' =>
                                            collect($values)
                                                ->map(
                                                    fn ($value) => [
                                                        'name' => $value,
                                                    ]
                                                )
                                                ->all(),

                                    ];

                                }

                            }

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | NORMALISASI GROUP NAME
                        |--------------------------------------------------------------------------
                        */

                        $variantGroupNames =
                            collect($variantGroups)
                                ->values()
                                ->map(
                                    fn ($group, $groupIndex) =>
                                        trim(
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
                                        )
                                )
                                ->all();

                    @endphp


                    <div
                        class="product-item"
                        data-product-name="{{ strtolower($product->name) }}"
                        data-product-sku="{{ strtolower($product->sku) }}"
                    >

                        {{-- ================================================= --}}
                        {{-- PRODUCT HEADER --}}
                        {{-- ================================================= --}}

                        <button
                            type="button"
                            class="product-toggle w-full p-4 sm:p-5 text-left hover:bg-gray-50 transition"
                        >

                            <div class="flex items-center justify-between gap-4">

                                <div class="flex items-center gap-3 min-w-0">

                                    {{-- ICON / IMAGE --}}

                                    @if ($product->image)

                                        <img
                                            src="{{ asset('storage/' . $product->image) }}"
                                            alt="{{ $product->name }}"
                                            class="w-10 h-10 rounded-lg object-cover border border-gray-200 shrink-0"
                                        >

                                    @else

                                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                                />
                                            </svg>

                                        </div>

                                    @endif


                                    {{-- PRODUCT INFO --}}

                                    <div class="min-w-0">

                                        <div class="flex items-center gap-2 flex-wrap">

                                            <h3 class="font-semibold text-sm text-gray-800">
                                                {{ $product->name }}
                                            </h3>

                                            @if (!$variants->count())

                                                <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-600">
                                                    Tidak ada variant
                                                </span>

                                            @endif

                                        </div>


                                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 flex-wrap">

                                            <span>
                                                SKU: {{ $product->sku }}
                                            </span>

                                            <span>
                                                •
                                            </span>

                                            <span>
                                                {{ $variants->count() }} kombinasi
                                            </span>

                                        </div>

                                    </div>

                                </div>


                                {{-- RIGHT --}}

                                <div class="flex items-center gap-3 shrink-0">

                                    <div class="hidden sm:block text-right">

                                        <p class="text-xs text-gray-400">
                                            Total stok
                                        </p>

                                        <p class="text-sm font-semibold text-gray-700">

                                            {{ $variants->sum('stock') }}

                                            {{ $product->unit }}

                                        </p>

                                    </div>


                                    <svg
                                        class="product-chevron w-5 h-5 text-gray-400 transition-transform duration-200"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m19 9-7 7-7-7"
                                        />
                                    </svg>

                                </div>

                            </div>

                        </button>


                        {{-- ================================================= --}}
                        {{-- VARIANT CONTAINER --}}
                        {{-- ================================================= --}}

                        <div class="variant-container hidden bg-gray-50 border-t border-gray-100">

                            @if ($variants->count())

                                <div class="overflow-x-auto">

                                    <table class="min-w-full text-sm">

                                        <thead class="bg-gray-100 text-gray-600">

                                            <tr>

                                                <th class="p-3 text-left font-medium whitespace-nowrap">
                                                    SKU Variant
                                                </th>


                                                {{-- DYNAMIC VARIANT HEADERS --}}

                                                @if (!empty($variantGroupNames))

                                                    @foreach ($variantGroupNames as $groupName)

                                                        <th class="p-3 text-left font-medium whitespace-nowrap">
                                                            {{ $groupName }}
                                                        </th>

                                                    @endforeach

                                                @else

                                                    <th class="p-3 text-left font-medium whitespace-nowrap">
                                                        Variant
                                                    </th>

                                                @endif


                                                <th class="p-3 text-center font-medium whitespace-nowrap">
                                                    Stok Sistem
                                                </th>

                                                <th class="p-3 text-center font-medium whitespace-nowrap">
                                                    Stok Fisik
                                                </th>

                                                <th class="p-3 text-center font-medium whitespace-nowrap">
                                                    Selisih
                                                </th>

                                                <th class="p-3 text-left font-medium min-w-[180px]">
                                                    Catatan
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody class="divide-y divide-gray-200">

                                            @foreach ($variants as $variant)

                                                @php

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | ATTRIBUTES
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    $attributes =
                                                        $variant->attributes ?? [];

                                                    if (is_string($attributes)) {

                                                        $decoded =
                                                            json_decode(
                                                                $attributes,
                                                                true
                                                            );

                                                        $attributes =
                                                            is_array($decoded)
                                                                ? $decoded
                                                                : [];

                                                    }

                                                    $attributes =
                                                        is_array($attributes)
                                                            ? array_values($attributes)
                                                            : [];

                                                @endphp


                                                <tr
                                                    class="hover:bg-white transition"
                                                    data-row
                                                >

                                                    {{-- ================================================= --}}
                                                    {{-- SKU --}}
                                                    {{-- ================================================= --}}

                                                    <td class="p-3 text-gray-500 whitespace-nowrap">

                                                        <input
                                                            type="hidden"
                                                            name="variants[{{ $index }}][product_variant_id]"
                                                            value="{{ $variant->id }}"
                                                        >

                                                        <span class="font-medium">
                                                            {{ $variant->sku_variant }}
                                                        </span>

                                                    </td>


                                                    {{-- ================================================= --}}
                                                    {{-- DYNAMIC ATTRIBUTES --}}
                                                    {{-- ================================================= --}}

                                                    @if (!empty($variantGroupNames))

                                                        @foreach ($variantGroupNames as $attributeIndex => $groupName)

                                                            <td class="p-3 text-gray-700 whitespace-nowrap">

                                                                @php
                                                                    $attributeValue =
                                                                        $attributes[$attributeIndex]
                                                                        ?? '-';
                                                                @endphp


                                                                <div class="flex items-center gap-2">

                                                                    @php

                                                                        /*
                                                                        | Cari gambar value dari variant group
                                                                        */

                                                                        $attributeImage = null;

                                                                        $currentGroup =
                                                                            $variantGroups[$attributeIndex]
                                                                            ?? null;

                                                                        if ($currentGroup) {

                                                                            foreach (
                                                                                $currentGroup['values'] ?? []
                                                                                as $groupValue
                                                                            ) {

                                                                                if (is_array($groupValue)) {

                                                                                    $groupValueName =
                                                                                        trim(
                                                                                            (string) (
                                                                                                $groupValue['name']
                                                                                                ?? ''
                                                                                            )
                                                                                        );

                                                                                    if (
                                                                                        strcasecmp(
                                                                                            $groupValueName,
                                                                                            trim((string) $attributeValue)
                                                                                        ) === 0
                                                                                    ) {

                                                                                        $attributeImage =
                                                                                            $groupValue['image']
                                                                                            ?? null;

                                                                                        break;

                                                                                    }

                                                                                }

                                                                            }

                                                                        }

                                                                    @endphp


                                                                    @if ($attributeImage)

                                                                        <img
                                                                            src="{{ asset('storage/' . $attributeImage) }}"
                                                                            alt="{{ $attributeValue }}"
                                                                            class="w-8 h-8 rounded-lg object-cover border border-gray-200 shrink-0"
                                                                        >

                                                                    @endif


                                                                    <span class="font-medium">
                                                                        {{ $attributeValue }}
                                                                    </span>

                                                                </div>

                                                            </td>

                                                        @endforeach

                                                    @else

                                                        <td class="p-3 text-gray-700">
                                                            -
                                                        </td>

                                                    @endif


                                                    {{-- ================================================= --}}
                                                    {{-- SYSTEM STOCK --}}
                                                    {{-- ================================================= --}}

                                                    <td class="p-3 text-center">

                                                        <span class="system-stock text-gray-700 font-semibold">

                                                            {{ $variant->stock }}

                                                        </span>

                                                        <span class="text-xs text-gray-400 ml-1">
                                                            {{ $product->unit }}
                                                        </span>

                                                    </td>


                                                    {{-- ================================================= --}}
                                                    {{-- PHYSICAL STOCK --}}
                                                    {{-- ================================================= --}}

                                                    <td class="p-3">

                                                        <input
                                                            type="number"
                                                            name="variants[{{ $index }}][physical_stock]"
                                                            value="{{ old("variants.$index.physical_stock", $variant->stock) }}"
                                                            min="0"
                                                            required
                                                            class="physical-stock w-24 mx-auto block border border-gray-300 rounded-lg px-2 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white"
                                                            data-system="{{ $variant->stock }}"
                                                        >

                                                    </td>


                                                    {{-- ================================================= --}}
                                                    {{-- DIFFERENCE --}}
                                                    {{-- ================================================= --}}

                                                    <td class="p-3 text-center">

                                                        <span
                                                            class="difference inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500"
                                                        >
                                                            0
                                                        </span>

                                                    </td>


                                                    {{-- ================================================= --}}
                                                    {{-- NOTE --}}
                                                    {{-- ================================================= --}}

                                                    <td class="p-3">

                                                        <input
                                                            type="text"
                                                            name="variants[{{ $index }}][note]"
                                                            value="{{ old("variants.$index.note") }}"
                                                            placeholder="Catatan"
                                                            class="w-full border border-gray-300 bg-white rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                                        >

                                                    </td>

                                                </tr>


                                                @php
                                                    $index++;
                                                @endphp

                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            @else

                                <div class="p-6 text-center">

                                    <p class="text-sm text-gray-500">
                                        Produk ini belum memiliki variant.
                                    </p>

                                    <p class="text-xs text-gray-400 mt-1">
                                        Tambahkan variant terlebih dahulu melalui Edit Produk.
                                    </p>

                                </div>

                            @endif

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center">

                        <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-6 h-6 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.8"
                                    d="M20 13V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.7-.9L12 2.5l-.8.6a2 2 0 01-1.2.4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-4"
                                />
                            </svg>

                        </div>

                        <p class="text-sm font-medium text-gray-600">
                            Tidak ada produk
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Belum ada produk yang dapat diperiksa.
                        </p>

                    </div>

                @endforelse


                {{-- SEARCH EMPTY --}}

                <div
                    id="search-empty"
                    class="hidden p-10 text-center"
                >

                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0010.6 10.6Z"
                            />
                        </svg>

                    </div>

                    <p class="text-sm font-medium text-gray-600">
                        Produk tidak ditemukan
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Coba gunakan nama produk atau SKU lain.
                    </p>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- FOOTER --}}
            {{-- ================================================= --}}

            <div class="p-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                <a
                    href="{{ route('admin.stock-opname.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-lg transition text-center"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    id="submit-button"
                    class="px-5 py-2.5 text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition"
                >
                    Simpan Stock Opname
                </button>

            </div>

        </div>

    </form>

</div>


{{-- ============================================================= --}}
{{-- SCRIPT --}}
{{-- ============================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ACCORDION
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.product-toggle').forEach(function (button) {

        button.addEventListener('click', function () {

            const productItem =
                this.closest('.product-item');

            const variantContainer =
                productItem.querySelector(
                    '.variant-container'
                );

            const chevron =
                productItem.querySelector(
                    '.product-chevron'
                );


            if (!variantContainer) {
                return;
            }


            const isHidden =
                variantContainer.classList.contains(
                    'hidden'
                );


            /*
            |--------------------------------------------------------------------------
            | TUTUP PRODUK LAIN
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('.variant-container')
                .forEach(function (container) {

                    if (
                        container !==
                        variantContainer
                    ) {

                        container.classList.add(
                            'hidden'
                        );

                    }

                });


            document
                .querySelectorAll('.product-chevron')
                .forEach(function (icon) {

                    if (icon !== chevron) {

                        icon.classList.remove(
                            'rotate-180'
                        );

                    }

                });


            /*
            |--------------------------------------------------------------------------
            | TOGGLE
            |--------------------------------------------------------------------------
            */

            if (isHidden) {

                variantContainer.classList.remove(
                    'hidden'
                );

                if (chevron) {

                    chevron.classList.add(
                        'rotate-180'
                    );

                }

            } else {

                variantContainer.classList.add(
                    'hidden'
                );

                if (chevron) {

                    chevron.classList.remove(
                        'rotate-180'
                    );

                }

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById(
            'product-search'
        );

    const productItems =
        document.querySelectorAll(
            '.product-item'
        );

    const productCount =
        document.getElementById(
            'product-count'
        );

    const searchEmpty =
        document.getElementById(
            'search-empty'
        );


    function searchProducts() {

        const keyword =
            searchInput
                ? searchInput.value
                    .toLowerCase()
                    .trim()
                : '';


        let visibleCount = 0;


        productItems.forEach(function (item) {

            const name =
                item.dataset.productName || '';

            const sku =
                item.dataset.productSku || '';


            const matched =
                keyword === ''
                ||
                name.includes(keyword)
                ||
                sku.includes(keyword);


            if (matched) {

                item.classList.remove(
                    'hidden'
                );

                visibleCount++;

            } else {

                item.classList.add(
                    'hidden'
                );


                const variantContainer =
                    item.querySelector(
                        '.variant-container'
                    );

                const chevron =
                    item.querySelector(
                        '.product-chevron'
                    );


                if (variantContainer) {

                    variantContainer.classList.add(
                        'hidden'
                    );

                }


                if (chevron) {

                    chevron.classList.remove(
                        'rotate-180'
                    );

                }

            }

        });


        if (productCount) {

            productCount.textContent =
                visibleCount;

        }


        if (searchEmpty) {

            if (
                visibleCount === 0
                &&
                keyword !== ''
            ) {

                searchEmpty.classList.remove(
                    'hidden'
                );

            } else {

                searchEmpty.classList.add(
                    'hidden'
                );

            }

        }

    }


    if (searchInput) {

        searchInput.addEventListener(
            'input',
            searchProducts
        );

    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH DIFFERENCE COUNTER
    |--------------------------------------------------------------------------
    */

    function refreshDiffCount() {

        let total = 0;


        document
            .querySelectorAll('[data-row]')
            .forEach(function (row) {

                const badge =
                    row.querySelector(
                        '.difference'
                    );


                if (!badge) {
                    return;
                }


                const difference =
                    parseInt(
                        badge.dataset.value || '0'
                    );


                if (difference !== 0) {

                    total++;

                }

            });


        const counter =
            document.getElementById(
                'diff-count'
            );


        if (counter) {

            counter.textContent =
                total;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | HITUNG SELISIH
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.physical-stock')
        .forEach(function (input) {

            function calculateDifference() {

                const systemStock =
                    parseInt(
                        input.dataset.system
                    ) || 0;


                const physicalStock =
                    parseInt(
                        input.value
                    ) || 0;


                const difference =
                    physicalStock -
                    systemStock;


                const row =
                    input.closest('tr');


                if (!row) {
                    return;
                }


                const badge =
                    row.querySelector(
                        '.difference'
                    );


                if (!badge) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN NILAI
                |--------------------------------------------------------------------------
                */

                badge.dataset.value =
                    difference;


                badge.textContent =
                    difference > 0
                        ? '+' + difference
                        : difference;


                /*
                |--------------------------------------------------------------------------
                | RESET CLASS
                |--------------------------------------------------------------------------
                */

                badge.classList.remove(

                    'bg-green-100',
                    'text-green-700',

                    'bg-red-100',
                    'text-red-700',

                    'bg-gray-100',
                    'text-gray-500'

                );


                /*
                |--------------------------------------------------------------------------
                | WARNA
                |--------------------------------------------------------------------------
                */

                if (difference > 0) {

                    badge.classList.add(
                        'bg-green-100',
                        'text-green-700'
                    );

                } else if (difference < 0) {

                    badge.classList.add(
                        'bg-red-100',
                        'text-red-700'
                    );

                } else {

                    badge.classList.add(
                        'bg-gray-100',
                        'text-gray-500'
                    );

                }


                refreshDiffCount();

            }


            input.addEventListener(
                'input',
                calculateDifference
            );


            /*
            |--------------------------------------------------------------------------
            | INITIAL
            |--------------------------------------------------------------------------
            */

            calculateDifference();

        });


    /*
    |--------------------------------------------------------------------------
    | SUBMIT CONFIRMATION
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById(
            'stock-opname-form'
        );

    const submitButton =
        document.getElementById(
            'submit-button'
        );


    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                const confirmed =
                    confirm(
                        'Simpan hasil stock opname?\n\n' +
                        'Stok setiap variant akan disesuaikan dengan stok fisik.'
                    );


                if (!confirmed) {

                    event.preventDefault();

                    return;

                }


                if (submitButton) {

                    submitButton.disabled =
                        true;

                    submitButton.textContent =
                        'Menyimpan...';

                    submitButton.classList.add(
                        'opacity-60',
                        'cursor-not-allowed'
                    );

                }

            }
        );

    }


});

</script>

@endpush

@endsection
