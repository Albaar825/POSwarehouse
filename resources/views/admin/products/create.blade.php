@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">

            <a href="{{ route('products.index') }}"
                class="text-gray-500 hover:text-gray-800 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15.75 19.5 8.25 12l7.5-7.5" />

                </svg>

            </a>

            <h1 class="text-lg font-semibold text-gray-800">
                Tambah Produk
            </h1>

        </div>

        <p class="text-sm text-gray-500">
            Tambahkan produk baru beserta variant warna, size, stok, dan gambar.
        </p>
    </div>


    {{-- ERROR --}}
    @if ($errors->any())

        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">

            <p class="font-medium text-sm mb-2">
                Terdapat kesalahan:
            </p>

            <ul class="list-disc list-inside text-sm space-y-1">

                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('products.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="productForm">

        @csrf


        {{-- INFORMASI PRODUK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">

            <div class="px-5 py-4 border-b border-gray-100">

                <h2 class="font-semibold text-gray-800">
                    Informasi Produk
                </h2>

                <p class="text-xs text-gray-500 mt-1">
                    Informasi utama produk yang akan disimpan di warehouse.
                </p>

            </div>


            <div class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                    {{-- NAMA --}}
                    <div>

                        <label for="name"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Nama Produk
                            <span class="text-red-500">*</span>

                        </label>

                        <input type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Kaos Oversize Premium"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    </div>


                    {{-- SKU --}}
                    <div>

                        <label for="sku"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            SKU Produk
                            <span class="text-red-500">*</span>

                        </label>

                        <input type="text"
                            name="sku"
                            id="sku"
                            value="{{ old('sku') }}"
                            placeholder="Contoh: KOS-001"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        <p class="text-xs text-gray-400 mt-1">
                            SKU utama produk. SKU variant akan dibuat otomatis.
                        </p>

                    </div>


                    {{-- CATEGORY --}}
                    <div>

                        <label for="category_id"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Kategori

                        </label>

                        <select name="category_id"
                            id="category_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            @foreach ($categories as $category)

                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- UNIT --}}
                    <div>

                        <label for="unit"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Satuan
                            <span class="text-red-500">*</span>

                        </label>

                        <select name="unit"
                            id="unit"
                            required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="pcs"
                                {{ old('unit', 'pcs') === 'pcs' ? 'selected' : '' }}>
                                pcs
                            </option>

                            <option value="unit"
                                {{ old('unit') === 'unit' ? 'selected' : '' }}>
                                unit
                            </option>

                            <option value="box"
                                {{ old('unit') === 'box' ? 'selected' : '' }}>
                                box
                            </option>

                            <option value="lusin"
                                {{ old('unit') === 'lusin' ? 'selected' : '' }}>
                                lusin
                            </option>

                        </select>

                    </div>


                    {{-- HARGA BELI --}}
                    <div>

                        <label for="purchase_price"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Harga Beli
                            <span class="text-red-500">*</span>

                        </label>

                        <div class="relative">

                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                                Rp
                            </span>

                            <input type="number"
                                name="purchase_price"
                                id="purchase_price"
                                value="{{ old('purchase_price', 0) }}"
                                min="0"
                                required
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        </div>

                    </div>


                    {{-- HARGA JUAL --}}
                    <div>

                        <label for="price"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Harga Jual
                            <span class="text-red-500">*</span>

                        </label>

                        <div class="relative">

                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
                                Rp
                            </span>

                            <input type="number"
                                name="price"
                                id="price"
                                value="{{ old('price', 0) }}"
                                min="0"
                                required
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        </div>

                    </div>


                    {{-- MIN STOCK --}}
                    <div class="md:col-span-2">

                        <label for="min_stock"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Minimum Stok Produk
                            <span class="text-red-500">*</span>

                        </label>

                        <input type="number"
                            name="min_stock"
                            id="min_stock"
                            value="{{ old('min_stock', 0) }}"
                            min="0"
                            required
                            class="w-full md:w-1/2 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        <p class="text-xs text-gray-400 mt-1">
                            Berlaku untuk total stok seluruh variant produk.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- VARIANTS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-5">

            <div class="px-5 py-4 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-semibold text-gray-800">
                            Variant Produk
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Setiap variant dapat memiliki warna, size, stok, dan banyak gambar.
                        </p>

                    </div>

                    <button type="button"
                        id="addVariant"
                        class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">

                        + Tambah Variant

                    </button>

                </div>

            </div>


            <div class="p-5">

                {{-- VARIANT CONTAINER --}}
                <div id="variantsContainer"
                    class="space-y-4">

                    @php

                        $oldVariants = old('variants', [
                            [
                                'color' => '',
                                'size' => '',
                                'stock' => 0,
                            ]
                        ]);

                    @endphp


                    @foreach ($oldVariants as $index => $variant)

                        <div class="variant-row border border-gray-200 rounded-xl p-4 md:p-5"
                            data-index="{{ $index }}">

                            {{-- VARIANT TOP --}}
                            <div class="flex items-center justify-between mb-4">

                                <div>

                                    <h3 class="font-semibold text-gray-800">
                                        Variant #{{ $index + 1 }}
                                    </h3>

                                    <p class="text-xs text-gray-400 mt-1">
                                        Data warna, size, stok dan gambar variant.
                                    </p>

                                </div>


                                <button type="button"
                                    class="remove-variant inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm transition">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-4 h-4">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244-2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C7.91 2.718 7 3.702 7 4.882v.916m7.5 0a48.667 48.667 0 0 1-7.5 0" />

                                    </svg>

                                    Hapus

                                </button>

                            </div>


                            {{-- COLOR / SIZE / STOCK --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">


                                {{-- COLOR --}}
                                <div class="md:col-span-4">

                                    <label class="block text-sm font-medium text-gray-700 mb-1">

                                        Warna
                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input type="text"
                                        name="variants[{{ $index }}][color]"
                                        value="{{ $variant['color'] ?? '' }}"
                                        placeholder="Contoh: Hitam"
                                        class="variant-color w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                </div>


                                {{-- SIZE --}}
                                <div class="md:col-span-4">

                                    <label class="block text-sm font-medium text-gray-700 mb-1">

                                        Size

                                    </label>

                                    <select name="variants[{{ $index }}][size]"
                                        class="variant-size w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                        <option value="">
                                            -- Size --
                                        </option>

                                        @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'] as $size)

                                            <option value="{{ $size }}"
                                                {{ ($variant['size'] ?? '') === $size ? 'selected' : '' }}>

                                                {{ $size }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                {{-- STOCK --}}
                                <div class="md:col-span-4">

                                    <label class="block text-sm font-medium text-gray-700 mb-1">

                                        Stok
                                        <span class="text-red-500">*</span>

                                    </label>

                                    <input type="number"
                                        name="variants[{{ $index }}][stock]"
                                        value="{{ $variant['stock'] ?? 0 }}"
                                        min="0"
                                        required
                                        class="variant-stock w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                </div>

                            </div>


                            {{-- IMAGE VARIANT --}}
                            <div class="mt-5 pt-5 border-t border-gray-100">

                                <div class="mb-3">

                                    <label class="block text-sm font-medium text-gray-700">

                                        Gambar Variant

                                        <span class="text-gray-400 font-normal">
                                            (opsional)
                                        </span>

                                    </label>

                                    <p class="text-xs text-gray-400 mt-1">

                                        Upload beberapa gambar untuk warna
                                        <strong class="variant-color-label">
                                            {{ $variant['color'] ?: 'variant ini' }}
                                        </strong>.

                                    </p>

                                </div>


                                <input type="file"
                                    name="variants[{{ $index }}][images][]"
                                    class="variant-images w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    multiple>


                                <p class="text-xs text-gray-400 mt-1">

                                    Bisa memilih banyak gambar sekaligus.
                                    Format JPG, JPEG, PNG, WEBP.
                                    Maksimal 2 MB per gambar.

                                </p>


                                {{-- IMAGE PREVIEW --}}
                                <div class="image-preview-container mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- TOTAL STOCK --}}
                <div class="mt-5 pt-4 border-t border-gray-100">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                        <div>

                            <p class="text-sm font-medium text-gray-700">
                                Total Stok Produk
                            </p>

                            <p class="text-xs text-gray-400">
                                Otomatis dihitung dari seluruh variant.
                            </p>

                        </div>

                        <div class="text-lg font-semibold text-gray-800">

                            <span id="totalStock">
                                0
                            </span>

                            <span id="stockUnit">
                                pcs
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ACTION --}}
        <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

            <a href="{{ route('products.index') }}"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">

                Batal

            </a>

            <button type="submit"
                id="submitButton"
                class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition">

                Simpan Produk

            </button>

        </div>

    </form>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const variantsContainer = document.getElementById('variantsContainer');

    const addVariantButton = document.getElementById('addVariant');

    const totalStockElement = document.getElementById('totalStock');

    const stockUnitElement = document.getElementById('stockUnit');

    const unitSelect = document.getElementById('unit');

    const productForm = document.getElementById('productForm');

    const submitButton = document.getElementById('submitButton');

    let variantIndex = {{ count($oldVariants) }};


    /*
    |--------------------------------------------------------------------------
    | UPDATE TOTAL STOCK
    |--------------------------------------------------------------------------
    */

    function updateTotalStock() {

        let total = 0;

        const stockInputs =
            variantsContainer.querySelectorAll('.variant-stock');

        stockInputs.forEach(function (input) {

            const value = parseInt(input.value) || 0;

            total += value;

        });

        totalStockElement.textContent = total;

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE UNIT
    |--------------------------------------------------------------------------
    */

    function updateUnit() {

        stockUnitElement.textContent =
            unitSelect.value || 'pcs';

    }

    unitSelect.addEventListener('change', updateUnit);


    /*
    |--------------------------------------------------------------------------
    | UPDATE COLOR LABEL
    |--------------------------------------------------------------------------
    */

    function updateColorLabel(row) {

        const colorInput =
            row.querySelector('.variant-color');

        const label =
            row.querySelector('.variant-color-label');

        if (!colorInput || !label) {
            return;
        }

        const color =
            colorInput.value.trim();

        label.textContent =
            color || 'variant ini';

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE PREVIEW
    |--------------------------------------------------------------------------
    */

    function handleImagePreview(input) {

        const row =
            input.closest('.variant-row');

        const previewContainer =
            row.querySelector('.image-preview-container');

        previewContainer.innerHTML = '';

        const files =
            Array.from(input.files);

        const maxSize =
            2 * 1024 * 1024;

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/jpg',
            'image/webp'
        ];


        if (files.length === 0) {
            return;
        }


        let invalidFile = false;


        files.forEach(function (file, index) {

            if (!allowedTypes.includes(file.type)) {

                invalidFile = true;

                return;

            }


            if (file.size > maxSize) {

                invalidFile = true;

                return;

            }


            const wrapper =
                document.createElement('div');

            wrapper.className =
                'relative group';


            const image =
                document.createElement('img');

            image.className =
                'w-full h-32 object-cover rounded-lg border border-gray-200';


            const badge =
                document.createElement('div');

            badge.className =
                'absolute top-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded-md';

            badge.textContent =
                index === 0
                    ? 'Utama'
                    : index + 1;


            const reader =
                new FileReader();


            reader.onload =
                function (event) {

                    image.src =
                        event.target.result;

                };


            reader.readAsDataURL(file);


            wrapper.appendChild(image);

            wrapper.appendChild(badge);

            previewContainer.appendChild(wrapper);

        });


        if (invalidFile) {

            alert(
                'Setiap gambar harus JPG, JPEG, PNG, atau WEBP dan maksimal 2 MB per gambar.'
            );

            input.value = '';

            previewContainer.innerHTML = '';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ADD VARIANT
    |--------------------------------------------------------------------------
    */

    addVariantButton.addEventListener('click', function () {

        const index =
            variantIndex;


        const row =
            document.createElement('div');

        row.className =
            'variant-row border border-gray-200 rounded-xl p-4 md:p-5';

        row.dataset.index =
            index;


        row.innerHTML = `

            <div class="flex items-center justify-between mb-4">

                <div>

                    <h3 class="font-semibold text-gray-800">
                        Variant #${index + 1}
                    </h3>

                    <p class="text-xs text-gray-400 mt-1">
                        Data warna, size, stok dan gambar variant.
                    </p>

                </div>

                <button
                    type="button"
                    class="remove-variant inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm transition"
                >

                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-4 h-4">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244-2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.682-.107 1.022-.166m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C7.91 2.718 7 3.702 7 4.882v.916m7.5 0a48.667 48.667 0 0 1-7.5 0"
                        />

                    </svg>

                    Hapus

                </button>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                <div class="md:col-span-4">

                    <label class="block text-sm font-medium text-gray-700 mb-1">

                        Warna
                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="text"
                        name="variants[${index}][color]"
                        placeholder="Contoh: Hitam"
                        class="variant-color w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                </div>


                <div class="md:col-span-4">

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Size
                    </label>

                    <select
                        name="variants[${index}][size]"
                        class="variant-size w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                        <option value="">
                            -- Size --
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


                <div class="md:col-span-4">

                    <label class="block text-sm font-medium text-gray-700 mb-1">

                        Stok
                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="number"
                        name="variants[${index}][stock]"
                        value="0"
                        min="0"
                        required
                        class="variant-stock w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >

                </div>

            </div>


            <div class="mt-5 pt-5 border-t border-gray-100">

                <div class="mb-3">

                    <label class="block text-sm font-medium text-gray-700">

                        Gambar Variant

                        <span class="text-gray-400 font-normal">
                            (opsional)
                        </span>

                    </label>

                    <p class="text-xs text-gray-400 mt-1">

                        Upload beberapa gambar untuk warna
                        <strong class="variant-color-label">
                            variant ini
                        </strong>.

                    </p>

                </div>


                <input
                    type="file"
                    name="variants[${index}][images][]"
                    class="variant-images w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    accept="image/jpeg,image/png,image/jpg,image/webp"
                    multiple
                >


                <p class="text-xs text-gray-400 mt-1">

                    Bisa memilih banyak gambar sekaligus.
                    Format JPG, JPEG, PNG, WEBP.
                    Maksimal 2 MB per gambar.

                </p>


                <div class="image-preview-container mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                </div>

            </div>

        `;


        variantsContainer.appendChild(row);

        variantIndex++;

        updateTotalStock();

    });


    /*
    |--------------------------------------------------------------------------
    | REMOVE VARIANT
    |--------------------------------------------------------------------------
    */

    variantsContainer.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest('.remove-variant');

            if (!button) {
                return;
            }


            const rows =
                variantsContainer.querySelectorAll('.variant-row');


            if (rows.length <= 1) {

                alert(
                    'Minimal harus ada 1 variant produk.'
                );

                return;

            }


            button.closest('.variant-row').remove();

            updateTotalStock();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | STOCK CHANGE
    |--------------------------------------------------------------------------
    */

    variantsContainer.addEventListener(
        'input',
        function (event) {

            if (
                event.target.classList.contains(
                    'variant-stock'
                )
            ) {

                updateTotalStock();

            }


            if (
                event.target.classList.contains(
                    'variant-color'
                )
            ) {

                updateColorLabel(
                    event.target.closest('.variant-row')
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | IMAGE CHANGE
    |--------------------------------------------------------------------------
    */

    variantsContainer.addEventListener(
        'change',
        function (event) {

            if (
                event.target.classList.contains(
                    'variant-images'
                )
            ) {

                handleImagePreview(
                    event.target
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | FORM SUBMIT
    |--------------------------------------------------------------------------
    */

    productForm.addEventListener(
        'submit',
        function (event) {

            const rows =
                variantsContainer.querySelectorAll(
                    '.variant-row'
                );


            if (rows.length === 0) {

                event.preventDefault();

                alert(
                    'Minimal harus ada 1 variant produk.'
                );

                return;

            }


            let valid = true;


            rows.forEach(function (row) {

                const color =
                    row.querySelector('.variant-color');

                const size =
                    row.querySelector('.variant-size');

                const stock =
                    row.querySelector('.variant-stock');


                /*
                | Variant harus memiliki warna atau size
                */

                if (
                    color.value.trim() === '' &&
                    size.value.trim() === ''
                ) {

                    valid = false;

                    color.focus();

                }


                /*
                | Stock tidak boleh negatif
                */

                if (
                    parseInt(stock.value) < 0
                ) {

                    valid = false;

                    stock.focus();

                }

            });


            if (!valid) {

                event.preventDefault();

                alert(
                    'Setiap variant harus memiliki warna atau size dan stok yang valid.'
                );

                return;

            }


            /*
            | Prevent double submit
            */

            submitButton.disabled =
                true;

            submitButton.classList.add(
                'opacity-50',
                'cursor-not-allowed'
            );

            submitButton.textContent =
                'Menyimpan...';

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.variant-row')
        .forEach(function (row) {

            updateColorLabel(row);

        });


    updateTotalStock();

    updateUnit();

});

</script>

@endpush
