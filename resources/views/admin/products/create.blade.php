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
            Tambahkan produk baru beserta variant warna, size, dan stok.
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

                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
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

                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">
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
                    <div>

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
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        <p class="text-xs text-gray-400 mt-1">
                            Berlaku untuk total stok seluruh variant produk.
                        </p>

                    </div>


                    {{-- IMAGE --}}
                    <div>

                        <label for="image"
                            class="block text-sm font-medium text-gray-700 mb-1">

                            Gambar Produk

                        </label>

                        <input type="file"
                            name="image"
                            id="image"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        <p class="text-xs text-gray-400 mt-1">
                            JPG, JPEG, PNG, WEBP. Maksimal 2 MB.
                        </p>

                        {{-- IMAGE PREVIEW --}}
                        <div id="imagePreviewContainer"
                            class="hidden mt-3">

                            <img id="imagePreview"
                                src=""
                                alt="Preview"
                                class="w-32 h-32 object-cover rounded-lg border border-gray-200">

                        </div>

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
                            Tentukan warna, size, dan stok untuk setiap variant.
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

                {{-- VARIANT HEADER --}}
                <div class="hidden md:grid grid-cols-12 gap-3 mb-2 px-1">

                    <div class="col-span-4">
                        <span class="text-xs font-semibold text-gray-500 uppercase">
                            Warna
                        </span>
                    </div>

                    <div class="col-span-3">
                        <span class="text-xs font-semibold text-gray-500 uppercase">
                            Size
                        </span>
                    </div>

                    <div class="col-span-3">
                        <span class="text-xs font-semibold text-gray-500 uppercase">
                            Stok
                        </span>
                    </div>

                    <div class="col-span-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase">
                            Aksi
                        </span>
                    </div>

                </div>


                {{-- VARIANT CONTAINER --}}
                <div id="variantsContainer"
                    class="space-y-3">

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

                        <div class="variant-row border border-gray-200 rounded-lg p-4 md:p-3"
                            data-index="{{ $index }}">

                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">


                                {{-- COLOR --}}
                                <div class="md:col-span-4">

                                    <label class="block md:hidden text-sm font-medium text-gray-700 mb-1">
                                        Warna
                                    </label>

                                    <input type="text"
                                        name="variants[{{ $index }}][color]"
                                        value="{{ $variant['color'] ?? '' }}"
                                        placeholder="Contoh: Hitam"
                                        class="variant-color w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                </div>


                                {{-- SIZE --}}
                                <div class="md:col-span-3">

                                    <label class="block md:hidden text-sm font-medium text-gray-700 mb-1">
                                        Size
                                    </label>

                                    <select name="variants[{{ $index }}][size]"
                                        class="variant-size w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                        <option value="">
                                            -- Size --
                                        </option>

                                        <option value="XS"
                                            {{ ($variant['size'] ?? '') === 'XS' ? 'selected' : '' }}>
                                            XS
                                        </option>

                                        <option value="S"
                                            {{ ($variant['size'] ?? '') === 'S' ? 'selected' : '' }}>
                                            S
                                        </option>

                                        <option value="M"
                                            {{ ($variant['size'] ?? '') === 'M' ? 'selected' : '' }}>
                                            M
                                        </option>

                                        <option value="L"
                                            {{ ($variant['size'] ?? '') === 'L' ? 'selected' : '' }}>
                                            L
                                        </option>

                                        <option value="XL"
                                            {{ ($variant['size'] ?? '') === 'XL' ? 'selected' : '' }}>
                                            XL
                                        </option>

                                        <option value="XXL"
                                            {{ ($variant['size'] ?? '') === 'XXL' ? 'selected' : '' }}>
                                            XXL
                                        </option>

                                        <option value="XXXL"
                                            {{ ($variant['size'] ?? '') === 'XXXL' ? 'selected' : '' }}>
                                            XXXL
                                        </option>

                                    </select>

                                </div>


                                {{-- STOCK --}}
                                <div class="md:col-span-3">

                                    <label class="block md:hidden text-sm font-medium text-gray-700 mb-1">
                                        Stok
                                    </label>

                                    <input type="number"
                                        name="variants[{{ $index }}][stock]"
                                        value="{{ $variant['stock'] ?? 0 }}"
                                        min="0"
                                        required
                                        class="variant-stock w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                </div>


                                {{-- DELETE --}}
                                <div class="md:col-span-2">

                                    <button type="button"
                                        class="remove-variant w-full md:w-auto inline-flex items-center justify-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2.5 rounded-lg text-sm transition">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                            class="w-4 h-4">

                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C7.91 2.718 7 3.702 7 4.882v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />

                                        </svg>

                                        Hapus

                                    </button>

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
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
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

            const stockInputs = variantsContainer.querySelectorAll('.variant-stock');

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

            stockUnitElement.textContent = unitSelect.value || 'pcs';

        }

        unitSelect.addEventListener('change', updateUnit);

        /*
        |--------------------------------------------------------------------------
        | ADD VARIANT
        |--------------------------------------------------------------------------
        */

        addVariantButton.addEventListener('click', function () {

            const row = document.createElement('div');

            row.className = 'variant-row border border-gray-200 rounded-lg p-4 md:p-3';

            row.dataset.index = variantIndex;

            row.innerHTML = `

                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">

                    <div class="md:col-span-4">

                        <label class="block md:hidden text-sm font-medium text-gray-700 mb-1">
                            Warna
                        </label>

                        <input
                            type="text"
                            name="variants[${variantIndex}][color]"
                            placeholder="Contoh: Hitam"
                            class="variant-color w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                    </div>


                    <div class="md:col-span-3">

                        <label class="block md:hidden text-sm font-medium text-gray-700 mb-1">
                            Size
                        </label>

                        <select
                            name="variants[${variantIndex}][size]"
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


                    <div class="md:col-span-3">

                        <label class="block md:hidden text-sm font-medium text-gray-700 mb-1">
                            Stok
                        </label>

                        <input
                            type="number"
                            name="variants[${variantIndex}][stock]"
                            value="0"
                            min="0"
                            required
                            class="variant-stock w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >

                    </div>


                    <div class="md:col-span-2">

                        <button
                            type="button"
                            class="remove-variant w-full md:w-auto inline-flex items-center justify-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2.5 rounded-lg text-sm transition"
                        >

                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-4 h-4">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0C7.91 2.718 7 3.702 7 4.882v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />

                            </svg>

                            Hapus

                        </button>

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
        variantsContainer.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-variant');
            if (!button) {
                return;
            }
            const rows = variantsContainer.querySelectorAll('.variant-row');
            if (rows.length <= 1) {
                alert('Minimal harus ada 1 variant produk.');
                return;
            }
            button.closest('.variant-row').remove();
            updateTotalStock();
        });
        /*
        |--------------------------------------------------------------------------
        | UPDATE TOTAL WHEN STOCK CHANGES
        |--------------------------------------------------------------------------
        */
        variantsContainer.addEventListener('input', function (event) {
            if (event.target.classList.contains('variant-stock')) {
                updateTotalStock();
            }
        });
        /*
        |--------------------------------------------------------------------------
        | IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) {
                imagePreviewContainer.classList.add('hidden');
                imagePreview.src = '';
                return;
            }
            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('Ukuran gambar maksimal 2 MB.');
                this.value = '';
                imagePreviewContainer.classList.add('hidden');
                imagePreview.src = '';
                return;
            }
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/jpg',
                'image/webp'
            ];
            if (!allowedTypes.includes(file.type)) {
                alert('Format gambar harus JPG, JPEG, PNG, atau WEBP.');
                this.value = '';
                imagePreviewContainer.classList.add('hidden');
                imagePreview.src = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (event) {
                imagePreview.src = event.target.result;
                imagePreviewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
        /*
        |--------------------------------------------------------------------------
        | FORM SUBMIT
        |--------------------------------------------------------------------------
        */
        productForm.addEventListener('submit', function (event) {
            const rows = variantsContainer.querySelectorAll('.variant-row');
            if (rows.length === 0) {
                event.preventDefault();
                alert('Minimal harus ada 1 variant produk.');
                return;
            }
            let valid = true;
            rows.forEach(function (row) {
                const color = row.querySelector('.variant-color');
                const size = row.querySelector('.variant-size');
                const stock = row.querySelector('.variant-stock');
                /*
                | Variant minimal harus memiliki color atau size
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

                if (parseInt(stock.value) < 0) {

                    valid = false;

                    stock.focus();

                }

            });
            if (!valid) {

                event.preventDefault();

                alert('Setiap variant harus memiliki warna atau size dan stok yang valid.');

                return;

            }
            /*
            | Prevent double submit
            */

            submitButton.disabled = true;

            submitButton.classList.add('opacity-50', 'cursor-not-allowed');

            submitButton.textContent = 'Menyimpan...';

        });
        /*
        |--------------------------------------------------------------------------
        | INITIAL
        |--------------------------------------------------------------------------
        */

        updateTotalStock();

        updateUnit();

    });

</script>

@endpush
