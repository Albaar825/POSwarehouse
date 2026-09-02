@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                Detail Produk
            </h1>

            <p class="text-sm text-gray-500">
                Informasi lengkap produk warehouse
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Kembali
            </a>

            <a href="{{ route('products.edit', $product) }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Produk
            </a>

        </div>

    </div>


    {{-- INFORMASI PRODUK --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-5">

        <div class="p-5">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- GAMBAR PRODUK --}}
                <div class="lg:col-span-1">

                    <p class="text-xs font-medium text-gray-500 mb-2">
                        Foto Produk
                    </p>

                    <div class="w-full aspect-square max-w-sm rounded-xl bg-gray-100 overflow-hidden border border-gray-200">

                        @if ($product->image)

                            <img src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover">

                        @else

                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-16 h-16 mb-2">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.409 2.409M3.75 19.5h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />

                                </svg>

                                <span class="text-sm">
                                    Tidak ada foto
                                </span>

                            </div>

                        @endif

                    </div>

                </div>


                {{-- DETAIL PRODUK --}}
                <div class="lg:col-span-2">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                        {{-- NAMA PRODUK --}}
                        <div class="sm:col-span-2">

                            <p class="text-xs text-gray-500">
                                Nama Produk
                            </p>

                            <p class="text-lg font-semibold text-gray-800 mt-1">
                                {{ $product->name }}
                            </p>

                        </div>


                        {{-- SKU --}}
                        <div>

                            <p class="text-xs text-gray-500">
                                SKU
                            </p>

                            <p class="font-semibold text-gray-800 mt-1">
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

                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
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

                            <p class="font-semibold text-gray-800 mt-1">
                                Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                            </p>

                        </div>


                        {{-- HARGA JUAL --}}
                        <div>

                            <p class="text-xs text-gray-500">
                                Harga Jual
                            </p>

                            <p class="font-semibold text-gray-800 mt-1">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>

                        </div>


                        {{-- SATUAN --}}
                        <div>

                            <p class="text-xs text-gray-500">
                                Satuan
                            </p>

                            <p class="font-semibold text-gray-800 mt-1">
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

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">

                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                        Aktif

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">

                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>

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


    {{-- INFORMASI STOK --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

        <div class="mb-4">

            <h2 class="font-semibold text-gray-800">
                Informasi Stok
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Informasi jumlah stok dan batas minimum produk
            </p>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- STOK SAAT INI --}}
            <div class="border border-gray-100 rounded-lg p-4">

                <p class="text-xs text-gray-500">
                    Stok Saat Ini
                </p>

                <div class="mt-2">

                    @if ($product->isLowStock())

                        <p class="text-xl font-semibold text-red-600">
                            {{ $product->stock }}
                            <span class="text-sm font-medium">
                                {{ $product->unit }}
                            </span>
                        </p>

                        <p class="text-xs text-red-500 mt-1">
                            Stok menipis
                        </p>

                    @else

                        <p class="text-xl font-semibold text-gray-800">
                            {{ $product->stock }}
                            <span class="text-sm font-medium">
                                {{ $product->unit }}
                            </span>
                        </p>

                        <p class="text-xs text-gray-400 mt-1">
                            Stok tersedia
                        </p>

                    @endif

                </div>

            </div>


            {{-- MINIMUM STOK --}}
            <div class="border border-gray-100 rounded-lg p-4">

                <p class="text-xs text-gray-500">
                    Minimum Stok
                </p>

                <p class="text-xl font-semibold text-gray-800 mt-2">
                    {{ $product->min_stock }}
                    <span class="text-sm font-medium">
                        {{ $product->unit }}
                    </span>
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Batas minimum persediaan
                </p>

            </div>


            {{-- JUMLAH VARIANT --}}
            <div class="border border-gray-100 rounded-lg p-4">

                <p class="text-xs text-gray-500">
                    Jumlah Variant
                </p>

                <p class="text-xl font-semibold text-gray-800 mt-2">
                    {{ $product->variants->count() }}
                    <span class="text-sm font-medium">
                        Variant
                    </span>
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    Variant produk tersedia
                </p>

            </div>

        </div>

    </div>


    {{-- VARIANT PRODUK --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

        <div class="mb-4">

            <h2 class="font-semibold text-gray-800">
                Variant Produk
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Daftar variant yang dimiliki produk
            </p>

        </div>


        @if ($product->variants->count())

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="bg-gray-50 text-gray-600 text-left">

                            <th class="p-3">
                                #
                            </th>

                            <th class="p-3">
                                Warna
                            </th>

                            <th class="p-3">
                                Ukuran
                            </th>

                            <th class="p-3">
                                Stok
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($product->variants as $index => $variant)

                            <tr class="border-t hover:bg-gray-50">

                                <td class="p-3 text-gray-500">
                                    {{ $index + 1 }}
                                </td>


                                {{-- WARNA --}}
                                <td class="p-3">

                                    @if ($variant->color)

                                        <span class="inline-flex px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                            {{ $variant->color }}
                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- SIZE --}}
                                <td class="p-3">

                                    @if ($variant->size)

                                        <span class="inline-flex px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                            {{ $variant->size }}
                                        </span>

                                    @else

                                        <span class="text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- STOK --}}
                                <td class="p-3 font-medium text-gray-800">
                                    {{ $variant->stock ?? 0 }}
                                    {{ $product->unit }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-8 text-gray-500 text-sm">
                Belum ada variant untuk produk ini.
            </div>

        @endif

    </div>


    {{-- RIWAYAT STOK --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">

        <div class="mb-4">

            <h2 class="font-semibold text-gray-800">
                Riwayat Pergerakan Stok
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                10 pergerakan stok terakhir
            </p>

        </div>


        @if ($product->stockMovements->count())

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="bg-gray-50 text-gray-600 text-left">

                            <th class="p-3">
                                Tanggal
                            </th>

                            <th class="p-3">
                                Tipe
                            </th>

                            <th class="p-3">
                                Jumlah
                            </th>

                            <th class="p-3">
                                Sumber
                            </th>

                            <th class="p-3">
                                User
                            </th>

                            <th class="p-3">
                                Catatan
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($product->stockMovements->take(10) as $movement)

                            <tr class="border-t hover:bg-gray-50">

                                {{-- TANGGAL --}}
                                <td class="p-3 whitespace-nowrap">

                                    {{ $movement->created_at->format('d/m/Y H:i') }}

                                </td>


                                {{-- TIPE --}}
                                <td class="p-3">

                                    @if ($movement->type === 'in')

                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">

                                            Masuk

                                        </span>

                                    @elseif ($movement->type === 'out')

                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">

                                            Keluar

                                        </span>

                                    @else

                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">

                                            {{ ucfirst($movement->type) }}

                                        </span>

                                    @endif

                                </td>


                                {{-- JUMLAH --}}
                                <td class="p-3 font-medium whitespace-nowrap">

                                    @if ($movement->type === 'in')

                                        <span class="text-green-600">
                                            +{{ $movement->quantity }}
                                        </span>

                                    @elseif ($movement->type === 'out')

                                        <span class="text-red-600">
                                            -{{ $movement->quantity }}
                                        </span>

                                    @else

                                        {{ $movement->quantity }}

                                    @endif

                                    {{ $product->unit }}

                                </td>


                                {{-- SUMBER --}}
                                <td class="p-3">

                                    {{ ucfirst(str_replace('_', ' ', $movement->source)) }}

                                </td>


                                {{-- USER --}}
                                <td class="p-3">

                                    {{ $movement->user->name ?? '-' }}

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

            <div class="text-center py-8 text-gray-500 text-sm">

                Belum ada riwayat pergerakan stok.

            </div>

        @endif

    </div>

@endsection
