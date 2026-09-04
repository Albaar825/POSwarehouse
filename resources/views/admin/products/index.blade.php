@extends('layouts.app')

@section('title', 'Kelola Barang')

@push('head')
    <link rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                Data Produk
            </h1>

            <p class="text-sm text-gray-500">
                Kelola data master produk warehouse
            </p>
        </div>

        <a href="{{ route('products.create') }}"
            class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Tambah Produk
        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR MESSAGE --}}
    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
    @endif


    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-4 overflow-x-auto">

            <table id="productTable" class="w-full text-sm">

                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">

                        <th class="p-3">
                            Produk
                        </th>

                        <th class="p-3">
                            SKU
                        </th>

                        <th class="p-3">
                            Kategori
                        </th>

                        <th class="p-3">
                            Harga Beli
                        </th>

                        <th class="p-3">
                            Harga Jual
                        </th>

                        <th class="p-3">
                            Variant
                        </th>

                        <th class="p-3">
                            Stok
                        </th>

                        <th class="p-3">
                            Status
                        </th>

                        <th class="p-3">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody>

                    @foreach ($products as $product)

                        @php
                            /*
                             * Ambil gambar primary dari variant pertama.
                             * Jika tidak ada primary, ambil gambar pertama.
                             */
                            $productImage = null;

                            foreach ($product->variants as $variant) {
                                $primaryImage = $variant->images->firstWhere('is_primary', true);

                                if ($primaryImage) {
                                    $productImage = $primaryImage;
                                    break;
                                }

                                if (!$productImage && $variant->images->first()) {
                                    $productImage = $variant->images->first();
                                }
                            }
                        @endphp

                        <tr class="border-t hover:bg-gray-50 transition">

                            {{-- PRODUK --}}
                            <td class="p-3">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200">

                                        @if ($productImage)

                                            <img src="{{ $productImage->image_url }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover">

                                        @else

                                            <div
                                                class="w-full h-full flex items-center justify-center text-gray-400">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-6 h-6">

                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.409 2.409M3.75 19.5h16.5a1.5 1.5 0 0 1 1.5-1.5V6a1.5 1.5 0 0 1-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />

                                                </svg>

                                            </div>

                                        @endif

                                    </div>


                                    <div class="min-w-0">

                                        <p class="font-medium text-gray-800 truncate">
                                            {{ $product->name }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            {{ $product->unit }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            {{-- SKU --}}
                            <td class="p-3">

                                <span class="font-medium text-gray-700">
                                    {{ $product->sku }}
                                </span>

                            </td>


                            {{-- KATEGORI --}}
                            <td class="p-3">

                                @if ($product->category)

                                    <span
                                        class="inline-flex px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">

                                        {{ $product->category->name }}

                                    </span>

                                @else

                                    <span class="text-gray-400">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- HARGA BELI --}}
                            <td class="p-3 whitespace-nowrap">

                                Rp {{ number_format($product->purchase_price, 0, ',', '.') }}

                            </td>


                            {{-- HARGA JUAL --}}
                            <td class="p-3 whitespace-nowrap font-medium">

                                Rp {{ number_format($product->price, 0, ',', '.') }}

                            </td>


                            {{-- VARIANT --}}
                            <td class="p-3">

                                @php
                                    $variantCount = $product->variants->count();
                                @endphp

                                <div class="flex flex-col gap-1">

                                    <span class="font-medium text-gray-700">
                                        {{ $variantCount }} Variant
                                    </span>

                                    @if ($variantCount > 0)

                                        <div class="flex flex-wrap gap-1 max-w-xs">

                                            @foreach ($product->variants->take(4) as $variant)

                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-xs">

                                                    {{ $variant->label() ?: '-' }}

                                                </span>

                                            @endforeach


                                            @if ($variantCount > 4)

                                                <span class="text-xs text-gray-400">

                                                    +{{ $variantCount - 4 }} lainnya

                                                </span>

                                            @endif

                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- STOK --}}
                            <td class="p-3 whitespace-nowrap">

                                @if ($product->isLowStock())

                                    <div>

                                        <span class="font-semibold text-red-600">

                                            {{ $product->stock }}
                                            {{ $product->unit }}

                                        </span>

                                        <p class="text-xs text-red-500 mt-0.5">
                                            Stok menipis
                                        </p>

                                    </div>

                                @else

                                    <div>

                                        <span class="font-medium text-gray-800">

                                            {{ $product->stock }}
                                            {{ $product->unit }}

                                        </span>

                                        <p class="text-xs text-gray-400 mt-0.5">

                                            Min. {{ $product->min_stock }}

                                        </p>

                                    </div>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="p-3">

                                @if ($product->is_active)

                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">

                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                        Aktif

                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">

                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>

                                        Nonaktif

                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="p-3">

                                <div class="flex items-center gap-3 whitespace-nowrap">

                                    <a href="{{ route('products.show', $product) }}"
                                        class="text-gray-600 hover:text-gray-900 font-medium">
                                        Detail
                                    </a>

                                    <a href="{{ route('products.edit', $product) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </a>

                                    <form action="{{ route('products.destroy', $product) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus produk {{ $product->name }}? Semua variant dan gambar variant produk ini juga akan dihapus.')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800 font-medium">
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

@endsection


@push('scripts')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#productTable').DataTable({

                pageLength: 10,

                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],

                order: [
                    [0, 'asc']
                ],

                language: {

                    search: "Cari:",

                    searchPlaceholder: "Cari produk...",

                    lengthMenu: "Tampilkan _MENU_ data",

                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",

                    infoEmpty: "Menampilkan 0 - 0 dari 0 data",

                    infoFiltered: "(difilter dari _MAX_ total data)",

                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        previous: "Sebelumnya",
                        next: "Selanjutnya"
                    },

                    zeroRecords: "Produk tidak ditemukan",

                    emptyTable: "Belum ada data produk"
                }

            });

        });
    </script>

@endpush
