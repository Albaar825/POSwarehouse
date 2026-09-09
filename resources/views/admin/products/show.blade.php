@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | VARIANT GROUPS
    |--------------------------------------------------------------------------
    */

    $variantGroups = $product->variant_groups ?? [];


    /*
    |--------------------------------------------------------------------------
    | FALLBACK VARIANT GROUP
    |--------------------------------------------------------------------------
    |
    | Produk lama mungkin belum memiliki variant_groups.
    | Tetapi product_variants sudah memiliki attributes.
    |
    | Contoh:
    |
    | ["Hitam", "M"]
    | ["Hitam", "L"]
    | ["Hitam", "XL"]
    | ["Putih", "M"]
    | ["Putih", "L"]
    | ["Putih", "XL"]
    |
    | Maka otomatis dibentuk:
    |
    | Warna  -> Hitam, Putih
    | Ukuran -> M, L, XL
    |
    */

    if (
        empty($variantGroups)
        &&
        $product->variants->count()
    ) {

        $allAttributes = $product->variants
            ->map(function ($variant) {

                return is_array($variant->attributes)
                    ? array_values($variant->attributes)
                    : [];

            })
            ->filter(function ($attributes) {

                return !empty($attributes);

            })
            ->values();


        if ($allAttributes->isNotEmpty()) {

            $attributeCount = $allAttributes
                ->map(function ($attributes) {

                    return count($attributes);

                })
                ->max();


            for (
                $i = 0;
                $i < $attributeCount;
                $i++
            ) {

                $values = $allAttributes
                    ->map(function ($attributes) use ($i) {

                        return $attributes[$i] ?? null;

                    })
                    ->filter(function ($value) {

                        return $value !== null
                            && $value !== '';

                    })
                    ->unique()
                    ->values()
                    ->map(function ($value) {

                        return [
                            'name' => (string) $value,
                            'image' => null,
                        ];

                    })
                    ->all();


                if (!empty($values)) {

                    $groupName =
                        $i === 0
                            ? 'Warna'
                            : (
                                $i === 1
                                    ? 'Ukuran'
                                    : 'Variant ' . ($i + 1)
                            );


                    $variantGroups[] = [

                        'name' =>
                            $groupName,

                        'type' =>
                            $i === 0
                                ? 'parent'
                                : 'child',

                        'values' =>
                            $values,

                    ];

                }

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | PARENT VARIANT
    |--------------------------------------------------------------------------
    */

    $parentGroup =
        $variantGroups[0] ?? null;


    $parentValues =
        $parentGroup['values'] ?? [];


    /*
    |--------------------------------------------------------------------------
    | GAMBAR UTAMA
    |--------------------------------------------------------------------------
    */

    $mainImage = null;


    if (!empty($product->image)) {

        $mainImage =
            asset(
                'storage/' . $product->image
            );

    } else {

        foreach (
            $parentValues as $value
        ) {

            if (
                is_array($value)
                &&
                !empty($value['image'])
            ) {

                $mainImage =
                    asset(
                        'storage/' . $value['image']
                    );

                break;

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | JUMLAH GROUP VARIANT
    |--------------------------------------------------------------------------
    */

    $variantGroupCount =
        count($variantGroups);

@endphp


{{-- ============================================================= --}}
{{-- HEADER --}}
{{-- ============================================================= --}}

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

    <div>

        <h1 class="text-lg font-semibold text-gray-800">
            Detail Produk
        </h1>

        <p class="text-sm text-gray-500">
            Informasi lengkap produk warehouse
        </p>

    </div>


    <div class="flex gap-2">

        <a
            href="{{ route('products.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
        >
            Kembali
        </a>


        <a
            href="{{ route('products.edit', $product) }}"
            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
        >
            Edit Produk
        </a>

    </div>

</div>



{{-- ============================================================= --}}
{{-- INFORMASI PRODUK --}}
{{-- ============================================================= --}}

<div class="mb-5 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">

    <div class="p-5">

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


            {{-- ================================================= --}}
            {{-- FOTO PRODUK --}}
            {{-- ================================================= --}}

            <div class="lg:col-span-1">

                <p class="mb-2 text-xs font-medium text-gray-500">
                    Foto Produk
                </p>


                <div
                    class="aspect-square w-full max-w-sm overflow-hidden rounded-xl border border-gray-200 bg-gray-100"
                >

                    @if ($mainImage)

                        <img
                            src="{{ $mainImage }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        >

                    @else

                        <div class="flex h-full w-full flex-col items-center justify-center text-gray-400">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="mb-2 h-16 w-16"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.409 2.409M3.75 19.5h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"
                                />

                            </svg>


                            <span class="text-sm">
                                Tidak ada foto
                            </span>

                        </div>

                    @endif

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- DETAIL --}}
            {{-- ================================================= --}}

            <div class="lg:col-span-2">

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">


                    {{-- NAMA --}}

                    <div class="sm:col-span-2">

                        <p class="text-xs text-gray-500">
                            Nama Produk
                        </p>

                        <p class="mt-1 text-lg font-semibold text-gray-800">
                            {{ $product->name }}
                        </p>

                    </div>



                    {{-- SKU --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            SKU
                        </p>

                        <p class="mt-1 font-semibold text-gray-800">
                            {{ $product->sku }}
                        </p>

                    </div>



                    {{-- KATEGORI --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Kategori
                        </p>

                        <div class="mt-1">

                            @if ($product->category)

                                <span
                                    class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700"
                                >
                                    {{ $product->category->name }}
                                </span>

                            @else

                                <span class="text-gray-400">
                                    -
                                </span>

                            @endif

                        </div>

                    </div>



                    {{-- HARGA BELI --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Harga Beli
                        </p>

                        <p class="mt-1 font-semibold text-gray-800">

                            Rp
                            {{ number_format($product->purchase_price, 0, ',', '.') }}

                        </p>

                    </div>



                    {{-- HARGA JUAL --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Harga Jual
                        </p>

                        <p class="mt-1 font-semibold text-gray-800">

                            Rp
                            {{ number_format($product->price, 0, ',', '.') }}

                        </p>

                    </div>



                    {{-- SATUAN --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Satuan
                        </p>

                        <p class="mt-1 font-semibold text-gray-800">
                            {{ $product->unit }}
                        </p>

                    </div>



                    {{-- STATUS --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Status
                        </p>

                        <div class="mt-1">

                            @if ($product->is_active)

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700"
                                >

                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>

                                    Aktif

                                </span>

                            @else

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                >

                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>

                                    Nonaktif

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ============================================================= --}}
{{-- INFORMASI STOK --}}
{{-- ============================================================= --}}

<div class="mb-5 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">

    <div class="mb-4">

        <h2 class="font-semibold text-gray-800">
            Informasi Stok
        </h2>

        <p class="mt-1 text-xs text-gray-500">
            Informasi jumlah stok dan batas minimum produk
        </p>

    </div>


    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">


        {{-- STOK --}}

        <div class="rounded-lg border border-gray-100 p-4">

            <p class="text-xs text-gray-500">
                Stok Saat Ini
            </p>


            <div class="mt-2">

                @if ($product->isLowStock())

                    <p class="text-xl font-semibold text-red-600">

                        {{ number_format($product->stock, 0, ',', '.') }}

                        <span class="text-sm font-medium">
                            {{ $product->unit }}
                        </span>

                    </p>

                    <p class="mt-1 text-xs text-red-500">
                        Stok menipis
                    </p>

                @else

                    <p class="text-xl font-semibold text-gray-800">

                        {{ number_format($product->stock, 0, ',', '.') }}

                        <span class="text-sm font-medium">
                            {{ $product->unit }}
                        </span>

                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        Stok tersedia
                    </p>

                @endif

            </div>

        </div>



        {{-- MINIMUM --}}

        <div class="rounded-lg border border-gray-100 p-4">

            <p class="text-xs text-gray-500">
                Minimum Stok
            </p>

            <p class="mt-2 text-xl font-semibold text-gray-800">

                {{ number_format($product->min_stock, 0, ',', '.') }}

                <span class="text-sm font-medium">
                    {{ $product->unit }}
                </span>

            </p>

            <p class="mt-1 text-xs text-gray-400">
                Batas minimum persediaan
            </p>

        </div>



        {{-- JUMLAH KOMBINASI --}}

        <div class="rounded-lg border border-gray-100 p-4">

            <p class="text-xs text-gray-500">
                Jumlah Kombinasi
            </p>

            <p class="mt-2 text-xl font-semibold text-gray-800">

                {{ $product->variants->count() }}

                <span class="text-sm font-medium">
                    Kombinasi
                </span>

            </p>

            <p class="mt-1 text-xs text-gray-400">
                Kombinasi variant produk
            </p>

        </div>

    </div>

</div>



{{-- ============================================================= --}}
{{-- STRUKTUR VARIANT --}}
{{-- ============================================================= --}}

<div class="mb-5 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">

    <div class="mb-5">

        <h2 class="font-semibold text-gray-800">
            Struktur Variant
        </h2>

        <p class="mt-1 text-xs text-gray-500">
            Daftar jenis variant dan nilai yang digunakan produk
        </p>

    </div>


    @if (count($variantGroups) > 0)

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">

            @foreach ($variantGroups as $groupIndex => $group)

                @php

                    $groupName =
                        $group['name']
                        ?? (
                            $groupIndex === 0
                                ? 'Warna'
                                : 'Variant ' . ($groupIndex + 1)
                        );


                    $groupType =
                        $group['type']
                        ?? (
                            $groupIndex === 0
                                ? 'parent'
                                : 'child'
                        );


                    $groupValues =
                        $group['values']
                        ?? [];

                @endphp


                <div class="overflow-hidden rounded-xl border border-gray-200">


                    {{-- HEADER GROUP --}}

                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">

                        <div class="flex items-center gap-2">

                            <span
                                class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-xs font-semibold text-blue-700"
                            >
                                {{ $groupIndex + 1 }}
                            </span>


                            <div>

                                <p class="font-semibold text-gray-800">
                                    {{ $groupName }}
                                </p>


                                @if ($groupType === 'parent')

                                    <p class="text-[11px] text-blue-600">
                                        Variant Utama
                                    </p>

                                @else

                                    <p class="text-[11px] text-gray-400">
                                        Variant Tambahan
                                    </p>

                                @endif

                            </div>

                        </div>

                    </div>



                    {{-- VALUES --}}

                    <div class="p-4">

                        @if (count($groupValues) > 0)

                            <div class="flex flex-wrap gap-2">

                                @foreach ($groupValues as $value)

                                    @php

                                        $valueName =
                                            is_array($value)
                                                ? (
                                                    $value['name']
                                                    ?? '-'
                                                )
                                                : (string) $value;


                                        $valueImage =
                                            is_array($value)
                                                ? (
                                                    $value['image']
                                                    ?? null
                                                )
                                                : null;

                                    @endphp


                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-200 bg-white"
                                    >

                                        @if ($valueImage)

                                            <img
                                                src="{{ asset('storage/' . $valueImage) }}"
                                                alt="{{ $valueName }}"
                                                class="h-16 w-16 object-cover"
                                            >

                                        @endif


                                        <div class="px-2 py-1.5">

                                            <p class="text-xs font-medium text-gray-700">
                                                {{ $valueName }}
                                            </p>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <p class="text-xs text-gray-400">
                                Tidak ada value.
                            </p>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 py-8 text-center">

            <div class="text-3xl">
                📦
            </div>

            <p class="mt-2 text-sm font-medium text-gray-600">
                Produk ini tidak memiliki variant.
            </p>

            <p class="mt-1 text-xs text-gray-400">
                Produk dijual tanpa kombinasi variant.
            </p>

        </div>

    @endif

</div>



{{-- ============================================================= --}}
{{-- KOMBINASI VARIANT --}}
{{-- ============================================================= --}}

<div class="mb-5 rounded-xl border border-gray-100 bg-white p-5 shadow-sm">

    <div class="mb-5">

        <h2 class="font-semibold text-gray-800">
            Kombinasi Variant
        </h2>

        <p class="mt-1 text-xs text-gray-500">
            Detail kombinasi variant beserta SKU, harga, dan stok
        </p>

    </div>


    @if ($product->variants->count() > 0)

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>

                    <tr class="bg-gray-50 text-left text-gray-600">

                        <th class="whitespace-nowrap p-3">
                            #
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Kombinasi
                        </th>

                        <th class="whitespace-nowrap p-3">
                            SKU Variant
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Harga
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Stok
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach ($product->variants as $index => $variant)

                        @php

                            $attributes =
                                is_array($variant->attributes)
                                    ? array_values($variant->attributes)
                                    : [];

                        @endphp


                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">


                            {{-- INDEX --}}

                            <td class="p-3">

                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-gray-100 text-xs font-medium text-gray-600"
                                >
                                    {{ $index + 1 }}
                                </span>

                            </td>



                            {{-- KOMBINASI --}}

                            <td class="p-3">

                                @if (count($attributes) > 0)

                                    <div class="flex flex-wrap gap-2">

                                        @foreach ($attributes as $attributeIndex => $attributeValue)

                                            @php

                                                $attributeGroup =
                                                    $variantGroups[$attributeIndex]
                                                    ?? null;


                                                $attributeName =
                                                    is_array($attributeGroup)
                                                        ? (
                                                            $attributeGroup['name']
                                                            ?? (
                                                                $attributeIndex === 0
                                                                    ? 'Variant'
                                                                    : 'Variant ' . ($attributeIndex + 1)
                                                            )
                                                        )
                                                        : (
                                                            $attributeIndex === 0
                                                                ? 'Variant'
                                                                : 'Variant ' . ($attributeIndex + 1)
                                                        );

                                            @endphp


                                            <span
                                                class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2.5 py-1 text-xs text-gray-700"
                                            >

                                                <span class="text-gray-400">
                                                    {{ $attributeName }}:
                                                </span>

                                                <span class="font-medium">
                                                    {{ $attributeValue }}
                                                </span>

                                            </span>

                                        @endforeach

                                    </div>

                                @else

                                    <span class="text-xs text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>



                            {{-- SKU --}}

                            <td class="p-3">

                                <span class="font-medium text-gray-700">
                                    {{ $variant->sku_variant ?: '-' }}
                                </span>

                            </td>



                            {{-- HARGA --}}

                            <td class="whitespace-nowrap p-3">

                                Rp
                                {{ number_format(
                                    $variant->price ?? $product->price,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>



                            {{-- STOCK --}}

                            <td class="whitespace-nowrap p-3">

                                @if ($variant->stock <= 0)

                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700"
                                    >
                                        Habis
                                    </span>

                                @elseif (
                                    $variant->stock
                                    <= $product->min_stock
                                )

                                    <div>

                                        <span
                                            class="font-semibold text-red-600"
                                        >
                                            {{ number_format($variant->stock, 0, ',', '.') }}
                                            {{ $product->unit }}
                                        </span>

                                        <p class="mt-0.5 text-[11px] text-red-500">
                                            Stok menipis
                                        </p>

                                    </div>

                                @else

                                    <span class="font-semibold text-gray-800">

                                        {{ number_format($variant->stock, 0, ',', '.') }}

                                        {{ $product->unit }}

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 py-8 text-center">

            <p class="text-sm text-gray-500">
                Belum ada kombinasi variant untuk produk ini.
            </p>

        </div>

    @endif

</div>



{{-- ============================================================= --}}
{{-- RIWAYAT STOCK --}}
{{-- ============================================================= --}}

<div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">

    <div class="mb-4">

        <h2 class="font-semibold text-gray-800">
            Riwayat Pergerakan Stok
        </h2>

        <p class="mt-1 text-xs text-gray-500">
            10 pergerakan stok terakhir
        </p>

    </div>


    @if ($product->stockMovements->count() > 0)

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>

                    <tr class="bg-gray-50 text-left text-gray-600">

                        <th class="whitespace-nowrap p-3">
                            Tanggal
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Tipe
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Jumlah
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Sumber
                        </th>

                        <th class="whitespace-nowrap p-3">
                            User
                        </th>

                        <th class="whitespace-nowrap p-3">
                            Catatan
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach (
                        $product->stockMovements
                            ->sortByDesc('created_at')
                            ->take(10)
                        as $movement
                    )

                        <tr class="border-t border-gray-100 transition hover:bg-gray-50">


                            {{-- TANGGAL --}}

                            <td class="whitespace-nowrap p-3">

                                @if ($movement->created_at)

                                    {{ $movement->created_at->format('d/m/Y H:i') }}

                                @else

                                    -

                                @endif

                            </td>



                            {{-- TIPE --}}

                            <td class="p-3">

                                @if ($movement->type === 'in')

                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700"
                                    >
                                        Masuk
                                    </span>

                                @elseif ($movement->type === 'out')

                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700"
                                    >
                                        Keluar
                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600"
                                    >
                                        {{ ucfirst($movement->type) }}
                                    </span>

                                @endif

                            </td>



                            {{-- JUMLAH --}}

                            <td class="whitespace-nowrap p-3 font-medium">

                                @if ($movement->type === 'in')

                                    <span class="text-green-600">
                                        +{{ number_format($movement->quantity, 0, ',', '.') }}
                                    </span>

                                @elseif ($movement->type === 'out')

                                    <span class="text-red-600">
                                        -{{ number_format($movement->quantity, 0, ',', '.') }}
                                    </span>

                                @else

                                    {{ number_format($movement->quantity, 0, ',', '.') }}

                                @endif


                                {{ $product->unit }}

                            </td>



                            {{-- SOURCE --}}

                            <td class="p-3">

                                {{ ucfirst(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $movement->source ?? '-'
                                    )
                                ) }}

                            </td>



                            {{-- USER --}}

                            <td class="p-3">

                                @if ($movement->user)

                                    {{ $movement->user->name }}

                                @else

                                    -

                                @endif

                            </td>



                            {{-- CATATAN --}}

                            <td class="p-3">

                                {{ $movement->note ?? '-' }}

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 py-8 text-center">

            <p class="text-sm text-gray-500">
                Belum ada riwayat pergerakan stok.
            </p>

        </div>

    @endif

</div>

@endsection
