@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">

        <div>
            <h1 class="text-lg font-semibold text-gray-800">
                Edit Produk
            </h1>

            <p class="text-sm text-gray-500">
                Perbarui informasi produk warehouse
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('products.show', $product) }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Lihat Detail
            </a>

            <a href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Kembali
            </a>

        </div>

    </div>


    {{-- ERROR MESSAGE --}}
    @if ($errors->any())

        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">

            <p class="font-medium text-sm mb-1">
                Terdapat kesalahan:
            </p>

            <ul class="list-disc list-inside text-sm">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- FORM --}}
    <form action="{{ route('products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data">

        @csrf
        @method('PUT')


        {{-- INFORMASI PRODUK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-800">
                    Informasi Produk
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Perbarui informasi dasar produk
                </p>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                {{-- SKU --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        SKU
                    </label>

                    <input type="text"
                        name="sku"
                        value="{{ old('sku', $product->sku) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>

                    @error('sku')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- NAMA --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Produk
                    </label>

                    <input type="text"
                        name="name"
                        value="{{ old('name', $product->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>

                    @error('name')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- KATEGORI --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Kategori
                    </label>

                    <select name="category_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        @foreach ($categories as $category)

                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('category_id')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- SATUAN --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Satuan
                    </label>

                    <input type="text"
                        name="unit"
                        value="{{ old('unit', $product->unit) }}"
                        placeholder="Contoh: pcs, kg, box"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>

                    @error('unit')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- HARGA BELI --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Beli
                    </label>

                    <div class="relative">

                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                            Rp
                        </span>

                        <input type="number"
                            name="purchase_price"
                            value="{{ old('purchase_price', $product->purchase_price) }}"
                            min="0"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>

                    </div>

                    @error('purchase_price')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- HARGA JUAL --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Harga Jual
                    </label>

                    <div class="relative">

                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                            Rp
                        </span>

                        <input type="number"
                            name="price"
                            value="{{ old('price', $product->price) }}"
                            min="0"
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>

                    </div>

                    @error('price')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- STOK --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Stok Saat Ini
                    </label>

                    <div class="flex">

                        <input type="number"
                            value="{{ $product->stock }}"
                            class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-sm text-gray-600"
                            disabled>

                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        Stok tidak dapat diubah dari halaman ini.
                        Gunakan menu <strong>Stok / Stock Opname</strong>.
                    </p>

                </div>


                {{-- MINIMUM STOK --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Minimum Stok
                    </label>

                    <input type="number"
                        name="min_stock"
                        value="{{ old('min_stock', $product->min_stock) }}"
                        min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>

                    @error('min_stock')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- FOTO PRODUK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-800">
                    Foto Produk
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Ganti foto produk jika diperlukan
                </p>

            </div>


            <div class="flex flex-col sm:flex-row gap-5">


                {{-- FOTO SAAT INI --}}
                <div>

                    <p class="text-xs font-medium text-gray-500 mb-2">
                        Foto Saat Ini
                    </p>

                    <div class="w-32 h-32 rounded-xl bg-gray-100 overflow-hidden border border-gray-200">

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
                                    class="w-10 h-10">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.409 2.409M3.75 19.5h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />

                                </svg>

                                <span class="text-xs mt-1">
                                    Tidak ada foto
                                </span>

                            </div>

                        @endif

                    </div>

                </div>


                {{-- UPLOAD FOTO --}}
                <div class="flex-1">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Ganti Foto
                    </label>

                    <input type="file"
                        name="image"
                        accept="image/*"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">

                    <p class="mt-1 text-xs text-gray-500">
                        Kosongkan jika tidak ingin mengganti foto.
                    </p>

                    @error('image')

                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>

        </div>


        {{-- VARIANT --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

            <div class="mb-5">

                <h2 class="font-semibold text-gray-800">
                    Variant Produk
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Informasi variant produk yang tersedia
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

                                <tr class="border-t">

                                    <td class="p-3 text-gray-500">
                                        {{ $index + 1 }}
                                    </td>


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


                                    <td class="p-3 font-medium text-gray-800">

                                        @if (isset($variant->stock))

                                            {{ $variant->stock }}
                                            {{ $product->unit }}

                                        @else

                                            -

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="mt-4 bg-blue-50 border border-blue-100 rounded-lg px-4 py-3">

                    <p class="text-xs text-blue-700">
                        Variant saat ini ditampilkan sebagai informasi.
                        Pengelolaan variant dilakukan melalui fitur variant produk.
                    </p>

                </div>

            @else

                <div class="text-center py-8 text-gray-500 text-sm">
                    Produk ini belum memiliki variant.
                </div>

            @endif

        </div>


        {{-- STATUS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

            <div class="mb-4">

                <h2 class="font-semibold text-gray-800">
                    Status Produk
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Tentukan apakah produk dapat digunakan dalam transaksi
                </p>

            </div>


            <label class="inline-flex items-center gap-3 cursor-pointer">

                <input type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                <span>

                    <span class="block text-sm font-medium text-gray-700">
                        Produk aktif
                    </span>

                    <span class="block text-xs text-gray-500">
                        Produk aktif dapat digunakan dalam sistem.
                    </span>
                </span>
            </label>
        </div>
        {{-- ACTION --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2">

            <a href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Batal
            </a>

            <button type="submit"
                class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection
