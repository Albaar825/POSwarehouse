@extends('layouts.app')

@section('title', 'Detail Stock Opname')

@section('content')

<div class="max-w-6xl mx-auto p-4">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-1">

        <div class="flex items-center gap-2">

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
                        d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414-1.414L8.414 10l4.293 4.293a1 1 0 010 1.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"
                    />
                </svg>

            </a>

            <div>

                <h1 class="text-lg font-semibold text-gray-800">
                    Detail Stock Opname
                </h1>

                <p class="text-sm text-gray-500">
                    {{ $stockOpname->opname_date->format('d F Y') }}
                </p>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ACTION BUTTON --}}
        {{-- ========================================================= --}}

        <div class="flex items-center gap-2">

            @if ($stockOpname->status === 'draft')

                <a
                    href="{{ route('admin.stock-opname.edit', $stockOpname) }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13l-2.685.806.806-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z"
                        />
                    </svg>

                    Edit

                </a>

            @endif


            <a
                href="{{ route('admin.stock-opname.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-lg transition"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>

                Kembali

            </a>

        </div>

    </div>


    <p class="text-sm text-gray-500 mb-6 ml-7">
        Detail hasil pemeriksaan stok berdasarkan stock opname warehouse.
    </p>


    {{-- ========================================================= --}}
    {{-- INFORMASI OPNAME --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">

        <div class="flex items-center justify-between gap-3 flex-wrap mb-4">

            <div>

                <h2 class="font-semibold text-gray-800">
                    Informasi Opname
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Informasi pelaksanaan stock opname.
                </p>

            </div>


            {{-- STATUS --}}
            @if ($stockOpname->status === 'completed')

                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700"
                >

                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                    Selesai

                </span>

            @else

                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700"
                >

                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>

                    Draft

                </span>

            @endif

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- TANGGAL --}}
            <div class="bg-gray-50 rounded-lg p-4">

                <p class="text-xs text-gray-500 mb-1">
                    Tanggal Opname
                </p>

                <p class="font-medium text-gray-800">
                    {{ $stockOpname->opname_date->format('d/m/Y') }}
                </p>

            </div>


            {{-- PETUGAS --}}
            <div class="bg-gray-50 rounded-lg p-4">

                <p class="text-xs text-gray-500 mb-1">
                    Petugas
                </p>

                <p class="font-medium text-gray-800">
                    {{ $stockOpname->user->name ?? '-' }}
                </p>

            </div>


            {{-- JUMLAH BARANG --}}
            <div class="bg-gray-50 rounded-lg p-4">

                <p class="text-xs text-gray-500 mb-1">
                    Jumlah Varian
                </p>

                <p class="font-medium text-gray-800">

                    {{ $stockOpname->details->count() }}

                    <span class="font-normal text-gray-500">
                        varian
                    </span>

                </p>

            </div>

        </div>


        {{-- CATATAN --}}
        @if ($stockOpname->note)

            <div class="mt-4 pt-4 border-t border-gray-100">

                <p class="text-xs text-gray-500 mb-1">
                    Catatan
                </p>

                <p class="text-sm text-gray-700">
                    {{ $stockOpname->note }}
                </p>

            </div>

        @endif

    </div>


    {{-- ========================================================= --}}
    {{-- PEMERIKSAAN STOK --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- HEADER --}}
        <div class="p-6 border-b border-gray-100">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>

                    <h2 class="font-semibold text-gray-800">
                        Pemeriksaan Stok
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Perbandingan stok sistem dengan stok fisik warehouse.
                    </p>

                </div>


                {{-- RINGKASAN SELISIH --}}
                @php

                    $increaseCount = $stockOpname->details
                        ->where('difference', '>', 0)
                        ->count();

                    $decreaseCount = $stockOpname->details
                        ->where('difference', '<', 0)
                        ->count();

                    $sameCount = $stockOpname->details
                        ->where('difference', 0)
                        ->count();

                @endphp


                <div class="flex items-center gap-2 flex-wrap text-xs">

                    <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                        +{{ $increaseCount }} bertambah
                    </span>

                    <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                        {{ $decreaseCount }} berkurang
                    </span>

                    <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">
                        {{ $sameCount }} sesuai
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
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6Z"
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
                        Klik produk untuk melihat variannya.
                    </p>

                    <p class="text-xs text-gray-500">

                        <span id="product-count">
                            {{ $stockOpname->details->groupBy('product_id')->count() }}
                        </span>

                        produk

                    </p>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- PRODUCT LIST --}}
        {{-- ========================================================= --}}

        @php
            $productGroups = $stockOpname->details
                ->groupBy('product_id')
                ->values();
        @endphp


        <div
            id="product-list"
            class="divide-y divide-gray-100"
        >

            @forelse ($productGroups as $details)

                @php

                    $product = $details->first()->product;

                    $productName = $product->name ?? 'Produk';
                    $productSku = $product->sku ?? '-';
                    $productUnit = $product->unit ?? '';

                    $totalSystemStock = $details->sum('system_stock');
                    $totalPhysicalStock = $details->sum('physical_stock');

                @endphp


                <div
                    class="product-item"
                    data-product-name="{{ strtolower($productName) }}"
                    data-product-sku="{{ strtolower($productSku) }}"
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

                                {{-- ICON --}}
                                <div
                                    class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"
                                >

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


                                {{-- PRODUCT INFO --}}
                                <div class="min-w-0">

                                    <h3 class="font-semibold text-sm text-gray-800">
                                        {{ $productName }}
                                    </h3>

                                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">

                                        <span>
                                            SKU: {{ $productSku }}
                                        </span>

                                        <span>
                                            •
                                        </span>

                                        <span>
                                            {{ $details->count() }} varian
                                        </span>

                                    </div>

                                </div>

                            </div>


                            {{-- RIGHT --}}
                            <div class="flex items-center gap-3 shrink-0">

                                <div class="hidden sm:block text-right">

                                    <p class="text-xs text-gray-400">
                                        Stok sistem
                                    </p>

                                    <p class="text-sm font-semibold text-gray-700">

                                        {{ $totalSystemStock }}

                                        {{ $productUnit }}

                                    </p>

                                </div>


                                {{-- CHEVRON --}}
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

                        <div class="overflow-x-auto">

                            <table class="w-full text-sm">

                                <thead class="bg-gray-100 text-gray-600">

                                    <tr>

                                        <th class="p-3 text-left font-medium whitespace-nowrap">
                                            SKU Varian
                                        </th>

                                        <th class="p-3 text-left font-medium whitespace-nowrap">
                                            Warna / Size
                                        </th>

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

                                    @foreach ($details as $detail)

                                        @php
                                            $variant = $detail->variant;
                                            $difference = (int) $detail->difference;
                                        @endphp


                                        <tr class="hover:bg-white transition">

                                            {{-- SKU --}}
                                            <td class="p-3 text-gray-500 whitespace-nowrap">

                                                {{ $variant->sku_variant ?? $productSku }}

                                            </td>


                                            {{-- WARNA / SIZE --}}
                                            <td class="p-3 text-gray-700 whitespace-nowrap">

                                                @if ($variant)

                                                    {{ $variant->label() }}

                                                @else

                                                    -

                                                @endif

                                            </td>


                                            {{-- STOK SISTEM --}}
                                            <td class="p-3 text-center">

                                                <span class="text-gray-700 font-medium">
                                                    {{ $detail->system_stock }}
                                                </span>

                                                <span class="text-xs text-gray-400 ml-1">
                                                    {{ $productUnit }}
                                                </span>

                                            </td>


                                            {{-- STOK FISIK --}}
                                            <td class="p-3 text-center">

                                                <span class="inline-flex items-center justify-center min-w-[3rem] px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-800 font-medium">

                                                    {{ $detail->physical_stock }}

                                                </span>

                                                <span class="text-xs text-gray-400 ml-1">
                                                    {{ $productUnit }}
                                                </span>

                                            </td>


                                            {{-- SELISIH --}}
                                            <td class="p-3 text-center">

                                                @if ($difference > 0)

                                                    <span
                                                        class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"
                                                    >
                                                        +{{ $difference }}
                                                    </span>

                                                @elseif ($difference < 0)

                                                    <span
                                                        class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700"
                                                    >
                                                        {{ $difference }}
                                                    </span>

                                                @else

                                                    <span
                                                        class="inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500"
                                                    >
                                                        0
                                                    </span>

                                                @endif

                                            </td>


                                            {{-- CATATAN --}}
                                            <td class="p-3 text-gray-600">

                                                {{ $detail->note ?? '-' }}

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            @empty

                {{-- EMPTY --}}
                <div class="p-10 text-center">

                    <div
                        class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3"
                    >

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
                        Belum ada hasil pemeriksaan
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Tidak terdapat detail pada stock opname ini.
                    </p>

                </div>

            @endforelse


            {{-- SEARCH EMPTY --}}
            <div
                id="search-empty"
                class="hidden p-10 text-center"
            >

                <div
                    class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3"
                >

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
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6Z"
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


        {{-- ========================================================= --}}
        {{-- FOOTER --}}
        {{-- ========================================================= --}}

        <div class="p-4 border-t border-gray-100 flex justify-end">

            <a
                href="{{ route('admin.stock-opname.index') }}"
                class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-lg transition"
            >
                Kembali ke Riwayat
            </a>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- SCRIPT --}}
{{-- ========================================================= --}}

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
                productItem.querySelector('.variant-container');

            const chevron =
                productItem.querySelector('.product-chevron');

            if (!variantContainer) {
                return;
            }

            const isHidden =
                variantContainer.classList.contains('hidden');


            /*
            |--------------------------------------------------------------------------
            | TUTUP PRODUK LAIN
            |--------------------------------------------------------------------------
            */

            document.querySelectorAll('.variant-container')
                .forEach(function (container) {

                    if (container !== variantContainer) {

                        container.classList.add('hidden');

                    }

                });


            document.querySelectorAll('.product-chevron')
                .forEach(function (icon) {

                    if (icon !== chevron) {

                        icon.classList.remove('rotate-180');

                    }

                });


            /*
            |--------------------------------------------------------------------------
            | TOGGLE PRODUK
            |--------------------------------------------------------------------------
            */

            if (isHidden) {

                variantContainer.classList.remove('hidden');

                if (chevron) {

                    chevron.classList.add('rotate-180');

                }

            } else {

                variantContainer.classList.add('hidden');

                if (chevron) {

                    chevron.classList.remove('rotate-180');

                }

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | SEARCH PRODUCT
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById('product-search');

    const productItems =
        document.querySelectorAll('.product-item');

    const productCount =
        document.getElementById('product-count');

    const searchEmpty =
        document.getElementById('search-empty');


    function searchProducts() {

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();

        let visibleCount = 0;


        productItems.forEach(function (item) {

            const name =
                item.dataset.productName || '';

            const sku =
                item.dataset.productSku || '';


            const matched =
                keyword === '' ||
                name.includes(keyword) ||
                sku.includes(keyword);


            if (matched) {

                item.classList.remove('hidden');

                visibleCount++;

            } else {

                item.classList.add('hidden');


                /*
                |--------------------------------------------------------------------------
                | TUTUP VARIANT KETIKA PRODUK DISEMBUNYIKAN
                |--------------------------------------------------------------------------
                */

                const variantContainer =
                    item.querySelector('.variant-container');

                const chevron =
                    item.querySelector('.product-chevron');


                if (variantContainer) {

                    variantContainer.classList.add('hidden');

                }


                if (chevron) {

                    chevron.classList.remove('rotate-180');

                }

            }

        });


        if (productCount) {

            productCount.textContent =
                visibleCount;

        }


        if (searchEmpty) {

            if (
                visibleCount === 0 &&
                keyword !== ''
            ) {

                searchEmpty.classList.remove('hidden');

            } else {

                searchEmpty.classList.add('hidden');

            }

        }

    }


    if (searchInput) {

        searchInput.addEventListener(
            'input',
            searchProducts
        );

    }

});

</script>

@endpush

@endsection
