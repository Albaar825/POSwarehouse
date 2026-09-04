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


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))

        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>

    @endif


    {{-- FORM --}}
    <form action="{{ route('products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data"
        id="productForm">

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


                {{-- STOK TOTAL --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Total Stok
                    </label>

                    <input type="number"
                        id="totalStock"
                        value="{{ $product->stock }}"
                        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2 text-sm text-gray-600"
                        disabled>

                    <p class="mt-1 text-xs text-gray-500">
                        Total stok dihitung otomatis dari seluruh variant.
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


        {{-- VARIANT PRODUK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-5">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

                <div>

                    <h2 class="font-semibold text-gray-800">
                        Variant Produk
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Atur warna, ukuran, stok, dan foto untuk setiap variant.
                    </p>

                </div>

                <button type="button"
                    id="addVariant"
                    class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">

                    + Tambah Variant

                </button>

            </div>


            <div id="variantContainer" class="space-y-5">


                @foreach ($product->variants as $index => $variant)

                    <div class="variant-item border border-gray-200 rounded-xl p-4"
                        data-index="{{ $index }}">

                        {{-- HEADER VARIANT --}}
                        <div class="flex items-center justify-between mb-4">

                            <div>

                                <h3 class="font-medium text-gray-800 variant-title">
                                    Variant {{ $index + 1 }}
                                </h3>

                                <p class="text-xs text-gray-400 variant-label">
                                    {{ $variant->label() ?: 'Belum ada warna / ukuran' }}
                                </p>

                            </div>

                            <button type="button"
                                class="remove-variant text-sm text-red-600 hover:text-red-800 font-medium">

                                Hapus

                            </button>

                        </div>


                        {{-- HIDDEN ID --}}
                        <input type="hidden"
                            name="variants[{{ $index }}][id]"
                            value="{{ $variant->id }}">


                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


                            {{-- WARNA --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Warna
                                </label>

                                <input type="text"
                                    name="variants[{{ $index }}][color]"
                                    value="{{ old("variants.$index.color", $variant->color) }}"
                                    placeholder="Contoh: Hitam"
                                    class="variant-color w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            </div>


                            {{-- UKURAN --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Ukuran
                                </label>

                                <select name="variants[{{ $index }}][size]"
                                    class="variant-size w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                    <option value="">
                                        -- Pilih Ukuran --
                                    </option>

                                    @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)

                                        <option value="{{ $size }}"
                                            {{ old("variants.$index.size", $variant->size) == $size ? 'selected' : '' }}>

                                            {{ $size }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- STOK --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Stok
                                </label>

                                <input type="number"
                                    name="variants[{{ $index }}][stock]"
                                    value="{{ old("variants.$index.stock", $variant->stock) }}"
                                    min="0"
                                    class="variant-stock w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>

                            </div>

                        </div>


                        {{-- FOTO VARIANT --}}
                        <div class="mt-5">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Foto Variant
                            </label>

                            <p class="text-xs text-gray-500 mb-3">
                                Upload foto tambahan untuk variant ini. Maksimal 10 foto, masing-masing 2MB.
                            </p>


                            {{-- FOTO LAMA --}}
                            @if ($variant->images->count())

                                <div class="mb-4">

                                    <p class="text-xs font-medium text-gray-600 mb-2">
                                        Foto Saat Ini
                                    </p>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">

                                        @foreach ($variant->images as $image)

                                            <div class="relative group">

                                                <div class="aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100">

                                                    <img src="{{ $image->image_url }}"
                                                        alt="{{ $variant->label() }}"
                                                        class="w-full h-full object-cover">

                                                </div>


                                                @if ($image->is_primary)

                                                    <span
                                                        class="absolute top-1 left-1 px-1.5 py-0.5 bg-blue-600 text-white text-[10px] rounded">
                                                        Utama
                                                    </span>

                                                @endif

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif


                            {{-- UPLOAD FOTO BARU --}}
                            <input type="file"
                                name="variants[{{ $index }}][images][]"
                                class="variant-images w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                multiple>

                            <div class="image-preview grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 mt-3">
                            </div>

                        </div>


                        {{-- SKU VARIANT --}}
                        <div class="mt-4 bg-gray-50 rounded-lg px-3 py-2">

                            <p class="text-xs text-gray-500">
                                SKU Variant
                            </p>

                            <p class="text-sm font-medium text-gray-700 mt-0.5">
                                {{ $variant->sku_variant }}
                            </p>

                        </div>

                    </div>

                @endforeach


            </div>


            @if ($product->variants->count() === 0)

                <div id="emptyVariant"
                    class="border border-dashed border-gray-300 rounded-xl p-8 text-center">

                    <p class="text-sm text-gray-500">
                        Produk belum memiliki variant.
                    </p>

                    <button type="button"
                        id="addVariantEmpty"
                        class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">

                        + Tambah Variant

                    </button>

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
                    Tentukan apakah produk dapat digunakan dalam transaksi.
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


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('variantContainer');
    const addVariantButton = document.getElementById('addVariant');
    const addVariantEmpty = document.getElementById('addVariantEmpty');
    const totalStockInput = document.getElementById('totalStock');

    let variantIndex = {{ $product->variants->count() }};


    /*
    |--------------------------------------------------------------------------
    | TEMPLATE VARIANT BARU
    |--------------------------------------------------------------------------
    */

    function variantTemplate(index) {

        return `
            <div class="variant-item border border-gray-200 rounded-xl p-4"
                data-index="${index}">

                <div class="flex items-center justify-between mb-4">

                    <div>

                        <h3 class="font-medium text-gray-800 variant-title">
                            Variant
                        </h3>

                        <p class="text-xs text-gray-400 variant-label">
                            Belum ada warna / ukuran
                        </p>

                    </div>

                    <button type="button"
                        class="remove-variant text-sm text-red-600 hover:text-red-800 font-medium">

                        Hapus

                    </button>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


                    {{-- WARNA --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Warna
                        </label>

                        <input type="text"
                            name="variants[${index}][color]"
                            placeholder="Contoh: Hitam"
                            class="variant-color w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    </div>


                    {{-- UKURAN --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ukuran
                        </label>

                        <select name="variants[${index}][size]"
                            class="variant-size w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">
                                -- Pilih Ukuran --
                            </option>

                            <option value="XS">XS</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                            <option value="XXXL">XXXL</option>

                        </select>

                    </div>


                    {{-- STOK --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Stok
                        </label>

                        <input type="number"
                            name="variants[${index}][stock]"
                            value="0"
                            min="0"
                            class="variant-stock w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>

                    </div>

                </div>


                {{-- FOTO --}}
                <div class="mt-5">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Foto Variant
                    </label>

                    <p class="text-xs text-gray-500 mb-3">
                        Upload beberapa foto untuk variant ini.
                    </p>

                    <input type="file"
                        name="variants[${index}][images][]"
                        class="variant-images w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white"
                        accept="image/jpeg,image/png,image/jpg,image/webp"
                        multiple>

                    <div class="image-preview grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 mt-3">
                    </div>

                </div>

            </div>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | TAMBAH VARIANT
    |--------------------------------------------------------------------------
    */

    function addVariant() {

        const emptyVariant = document.getElementById('emptyVariant');

        if (emptyVariant) {
            emptyVariant.remove();
        }

        container.insertAdjacentHTML(
            'beforeend',
            variantTemplate(variantIndex)
        );

        variantIndex++;

        updateVariantNumbers();
        updateTotalStock();
    }


    if (addVariantButton) {
        addVariantButton.addEventListener('click', addVariant);
    }

    if (addVariantEmpty) {
        addVariantEmpty.addEventListener('click', addVariant);
    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS VARIANT
    |--------------------------------------------------------------------------
    */

    container.addEventListener('click', function (event) {

        if (!event.target.classList.contains('remove-variant')) {
            return;
        }

        const variant = event.target.closest('.variant-item');

        if (!variant) {
            return;
        }

        if (container.querySelectorAll('.variant-item').length <= 1) {

            alert('Minimal harus ada 1 variant.');

            return;
        }

        if (confirm('Yakin ingin menghapus variant ini?')) {

            variant.remove();

            updateVariantNumbers();
            updateTotalStock();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE NOMOR VARIANT
    |--------------------------------------------------------------------------
    */

    function updateVariantNumbers() {

        const variants =
            container.querySelectorAll('.variant-item');

        variants.forEach(function (variant, index) {

            const title =
                variant.querySelector('.variant-title');

            if (title) {
                title.textContent =
                    `Variant ${index + 1}`;
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE TOTAL STOK
    |--------------------------------------------------------------------------
    */

    function updateTotalStock() {

        let total = 0;

        container
            .querySelectorAll('.variant-stock')
            .forEach(function (input) {

                total += parseInt(input.value) || 0;

            });

        totalStockInput.value = total;

    }


    container.addEventListener('input', function (event) {

        if (event.target.classList.contains('variant-stock')) {
            updateTotalStock();
        }

    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE LABEL VARIANT
    |--------------------------------------------------------------------------
    */

    container.addEventListener('input', function (event) {

        if (!event.target.classList.contains('variant-color')) {
            return;
        }

        const variant =
            event.target.closest('.variant-item');

        updateVariantLabel(variant);

    });


    container.addEventListener('change', function (event) {

        if (!event.target.classList.contains('variant-size')) {
            return;
        }

        const variant =
            event.target.closest('.variant-item');

        updateVariantLabel(variant);

    });


    function updateVariantLabel(variant) {

        if (!variant) {
            return;
        }

        const color =
            variant.querySelector('.variant-color')?.value || '';

        const size =
            variant.querySelector('.variant-size')?.value || '';

        const label =
            variant.querySelector('.variant-label');

        if (!label) {
            return;
        }

        const values = [];

        if (color) {
            values.push(color);
        }

        if (size) {
            values.push(size);
        }

        label.textContent =
            values.length
                ? values.join(' / ')
                : 'Belum ada warna / ukuran';

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    container.addEventListener('change', function (event) {

        if (!event.target.classList.contains('variant-images')) {
            return;
        }

        const input = event.target;

        const variant =
            input.closest('.variant-item');

        const preview =
            variant.querySelector('.image-preview');

        preview.innerHTML = '';

        const files =
            Array.from(input.files);

        if (files.length > 10) {

            alert('Maksimal 10 foto untuk setiap variant.');

            input.value = '';

            return;
        }


        files.forEach(function (file) {

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {

                alert(
                    `${file.name} bukan format gambar yang didukung.`
                );

                return;
            }


            if (file.size > 2 * 1024 * 1024) {

                alert(
                    `${file.name} lebih dari 2MB.`
                );

                return;
            }


            const reader = new FileReader();

            reader.onload = function (e) {

                const wrapper =
                    document.createElement('div');

                wrapper.className =
                    'relative aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-100';


                const image =
                    document.createElement('img');

                image.src = e.target.result;

                image.className =
                    'w-full h-full object-cover';


                wrapper.appendChild(image);

                preview.appendChild(wrapper);

            };

            reader.readAsDataURL(file);

        });

    });


    /*
    |--------------------------------------------------------------------------
    | VALIDASI FORM
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('productForm')
        .addEventListener('submit', function (event) {

            const variants =
                container.querySelectorAll('.variant-item');

            if (variants.length === 0) {

                event.preventDefault();

                alert('Minimal harus ada 1 variant.');

                return;
            }


            let valid = true;


            variants.forEach(function (variant) {

                const color =
                    variant.querySelector('.variant-color')?.value.trim();

                const size =
                    variant.querySelector('.variant-size')?.value;

                const stock =
                    parseInt(
                        variant.querySelector('.variant-stock')?.value
                    );


                if (!color && !size) {

                    valid = false;

                    alert(
                        'Setiap variant harus memiliki warna atau ukuran.'
                    );

                    return;
                }


                if (isNaN(stock) || stock < 0) {

                    valid = false;

                    alert(
                        'Stok variant tidak boleh kurang dari 0.'
                    );

                }

            });


            if (!valid) {
                event.preventDefault();
            }

        });


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    container
        .querySelectorAll('.variant-item')
        .forEach(function (variant) {

            updateVariantLabel(variant);

        });

    updateTotalStock();
    updateVariantNumbers();

});

</script>

@endpush
