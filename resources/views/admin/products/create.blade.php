@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')

<div
    x-data="productCreateForm()"
    class="space-y-6"
>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Produk</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan produk beserta variant dan stoknya.</p>
        </div>

        <a href="{{ route('products.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4">
            <div class="flex gap-3">
                <div class="shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.71 2.14h17.64a2 2 0 001.71-2.14l-8.82-15a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-red-800">Ada data yang perlu diperbaiki</h3>
                    <ul class="mt-2 space-y-1 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" @submit="prepareSubmit()" class="space-y-6">
        @csrf

        {{-- INFORMASI PRODUK --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Produk</h2>
                        <p class="text-sm text-gray-500">Informasi dasar produk.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Hoodie Premium"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm bg-white focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SKU Produk</label>
                    <input type="text" name="sku" x-model="baseSku" value="{{ old('sku') }}" placeholder="Contoh: HD-PREM-001"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                    @error('sku') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Beli <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price', 0) }}" min="0" required
                            class="w-full rounded-xl border border-gray-300 pl-12 pr-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                    </div>
                    @error('purchase_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Harga Jual (default) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                        <input type="number" name="price" x-model.number="basePrice" value="{{ old('price', 0) }}" min="0" required
                            class="w-full rounded-xl border border-gray-300 pl-12 pr-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500">Dipakai sebagai default harga tiap kombinasi variant (bisa diubah per baris di bawah).</p>
                    @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0" required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                    <p class="mt-1.5 text-xs text-gray-500">Digunakan sebagai batas peringatan stok menipis.</p>
                    @error('min_stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Produk</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 hover:border-gray-500 transition">
                        <div class="flex flex-col sm:flex-row items-center gap-5">
                            <div class="w-28 h-28 rounded-2xl bg-gray-100 flex items-center justify-center overflow-hidden shrink-0">
                                <template x-if="mainImagePreview">
                                    <img :src="mainImagePreview" class="w-full h-full object-cover" alt="Preview">
                                </template>
                                <template x-if="!mainImagePreview">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5V7a2 2 0 012-2h3l1.5-2h5L16 5h3a2 2 0 012 2v9.5a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        <circle cx="12" cy="12" r="3" stroke-width="1.5" />
                                    </svg>
                                </template>
                            </div>
                            <div class="flex-1 w-full">
                                <input type="file" name="image" accept="image/*" @change="previewMainImage($event)"
                                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                                <p class="mt-2 text-xs text-gray-500">JPG, JPEG, PNG, WEBP. Gunakan gambar produk utama.</p>
                            </div>
                        </div>
                    </div>
                    @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- VARIANT --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Variant Produk</h2>
                            <p class="text-sm text-gray-500">Tambahkan warna, ukuran, atau variant lainnya.</p>
                        </div>
                    </div>

                    <button type="button" @click="addVariantType()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Variant
                    </button>
                </div>
            </div>

            <div class="p-6">

                <div x-show="variantTypes.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-white flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Belum ada variant</h3>
                    <p class="text-sm text-gray-500 mt-1">Klik "Tambah Variant" untuk menambahkan variant produk.</p>
                </div>

                <div class="space-y-5">
                    <template x-for="(variant, index) in variantTypes" :key="variant.id">
                        <div class="rounded-2xl border border-gray-200 overflow-hidden">

                            <div class="px-5 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gray-900 text-white flex items-center justify-center text-sm font-bold" x-text="index + 1"></div>
                                        <div>
                                            <h3 class="font-bold text-gray-900" x-text="'Variant ' + (index + 1)"></h3>
                                            <p class="text-xs text-gray-500" x-show="index === 0">Variant utama, contoh: Warna</p>
                                            <p class="text-xs text-gray-500" x-show="index > 0">Variant tambahan, contoh: Ukuran, Bahan, Model</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeVariantType(index)"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-500 hover:bg-red-50 transition" title="Hapus variant">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="p-5 space-y-5">

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Variant <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'variant_groups[' + index + '][name]'" x-model="variant.name" @input="refreshCombinations()"
                                        :placeholder="index === 0 ? 'Contoh: Warna' : 'Contoh: Ukuran'" required
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">

                                    {{-- WAJIB: field type ini yang divalidasi controller (parent/child) --}}
                                    <input type="hidden" :name="'variant_groups[' + index + '][type]'" :value="index === 0 ? 'parent' : 'child'">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700">Nilai Variant</label>
                                            <p class="text-xs text-gray-500 mt-1">Tambahkan nilai yang tersedia.</p>
                                        </div>
                                        <button type="button" @click="addVariantValue(index)"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Nilai
                                        </button>
                                    </div>

                                    <div class="space-y-3">
                                        <template x-for="(value, valueIndex) in variant.values" :key="value.id">
                                            <div class="variant-value-row rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                <div class="flex flex-col lg:flex-row gap-4">

                                                    <div class="flex-1">
                                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nilai</label>
                                                        <input type="text" :name="'variant_groups[' + index + '][values][' + valueIndex + '][name]'"
                                                            x-model="value.name" @input="refreshCombinations()"
                                                            :placeholder="index === 0 ? 'Contoh: Hitam' : 'Contoh: M'" required
                                                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition">
                                                    </div>

                                                    <div class="w-full lg:w-80" x-show="index === 0">
                                                        <label class="block text-xs font-semibold text-gray-600 mb-2">Gambar</label>
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-16 h-16 rounded-xl bg-white border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                                <template x-if="value.preview">
                                                                    <img :src="value.preview" class="w-full h-full object-cover" alt="">
                                                                </template>
                                                                <template x-if="!value.preview">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4-4a3 3 0 014 0l4 4m-1-5h.01M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                                                    </svg>
                                                                </template>
                                                            </div>
                                                            <input type="file" accept="image/*"
                                                                :name="'variant_groups[' + index + '][values][' + valueIndex + '][image]'"
                                                                @change="previewVariantImage($event, index, valueIndex)"
                                                                class="block w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                                                        </div>
                                                        <p class="mt-1.5 text-[11px] text-gray-500">Gambar khusus untuk nilai ini (misal: foto warna Hitam). Otomatis tampil di POS saat kasir memilih warna ini.</p>
                                                    </div>

                                                    <div class="flex items-end">
                                                        <button type="button" @click="removeVariantValue(index, valueIndex)"
                                                            class="w-full lg:w-10 h-10 rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition flex items-center justify-center" title="Hapus nilai">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
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

                <div x-show="variantTypes.length > 0" class="mt-5 rounded-xl bg-gray-50 border border-gray-200 p-4">
                    <div class="flex gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                        <div class="text-sm text-gray-600">
                            <p class="font-semibold text-gray-800">Tips Variant</p>
                            <p class="mt-1">Variant pertama biasanya digunakan untuk <strong>Warna</strong>. Jika ada variant kedua, gunakan <strong>Ukuran</strong>. Variant ketiga dan seterusnya dapat digunakan untuk kebutuhan lain.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- TABEL KOMBINASI STOK --}}
        <div x-show="variantTypes.length > 0 && combinations.length > 0" x-cloak class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Kombinasi Variant & Stok</h2>
                        <p class="text-sm text-gray-500 mt-1">Isi harga (opsional) dan stok untuk setiap kombinasi variant.</p>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-100 text-sm font-semibold text-gray-700">
                        <span>Total kombinasi:</span>
                        <span x-text="combinations.length" class="text-gray-900"></span>
                    </div>
                </div>
            </div>

            <div class="p-6">

                {{-- DESKTOP TABLE --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <template x-for="(variant, variantIndex) in variantTypes" :key="'head-' + variant.id">
                                    <th class="text-left py-3 px-3 font-semibold text-gray-600" x-text="variant.name || ('Variant ' + (variantIndex + 1))"></th>
                                </template>
                                <th class="text-left py-3 px-3 font-semibold text-gray-600">SKU Variant</th>
                                <th class="text-right py-3 px-3 font-semibold text-gray-600">Harga</th>
                                <th class="text-right py-3 px-3 font-semibold text-gray-600">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(combination, index) in combinations" :key="combination.id">
                                <tr class="hover:bg-gray-50 transition">

                                    <template x-for="(attribute, attributeIndex) in combination.attributes" :key="'attr-' + combination.id + '-' + attributeIndex">
                                        <td class="py-3 px-3">
                                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-gray-100 text-xs font-semibold text-gray-700" x-text="attribute"></span>
                                            {{-- WAJIB: kirim attribute value ini ke server --}}
                                            <input type="hidden" :name="'variants[' + index + '][attributes][' + attributeIndex + ']'" :value="attribute">
                                        </td>
                                    </template>

                                    <td class="py-3 px-3">
                                        <input type="text" :name="'variants[' + index + '][sku_variant]'" x-model="combination.sku_variant" :value="combination.sku_variant" placeholder="SKU"
                                            class="w-full min-w-[160px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none">
                                    </td>

                                    <td class="py-3 px-3">
                                        <div class="relative">
                                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-400">Rp</span>
                                            <input type="number" min="0" :name="'variants[' + index + '][price]'" x-model.number="combination.price" :placeholder="basePrice"
                                                class="w-28 ml-auto block rounded-lg border border-gray-300 pl-7 pr-2 py-2 text-sm text-right focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none">
                                        </div>
                                    </td>

                                    <td class="py-3 px-3">
                                        <input type="number" min="0" :name="'variants[' + index + '][stock]'" x-model.number="combination.stock" required
                                            class="w-28 ml-auto block rounded-lg border border-gray-300 px-3 py-2 text-sm text-right focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none">
                                    </td>

                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE CARD --}}
                <div class="md:hidden space-y-4">
                    <template x-for="(combination, index) in combinations" :key="'mobile-' + combination.id">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-semibold text-gray-500" x-text="'Kombinasi #' + (index + 1)"></span>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(attribute, attributeIndex) in combination.attributes" :key="'mobile-attr-' + combination.id + '-' + attributeIndex">
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-xs text-gray-500" x-text="variantTypes[attributeIndex]?.name || ('Variant ' + (attributeIndex + 1))"></span>
                                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-gray-100 text-xs font-semibold text-gray-700" x-text="attribute"></span>
                                        <input type="hidden" :name="'variants[' + index + '][attributes][' + attributeIndex + ']'" :value="attribute">
                                    </div>
                                </template>

                                <div class="pt-3 border-t border-gray-100">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SKU Variant</label>
                                    <input type="text" :name="'variants[' + index + '][sku_variant]'" x-model="combination.sku_variant"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none" placeholder="SKU Variant">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Harga (opsional)</label>
                                    <input type="number" min="0" :name="'variants[' + index + '][price]'" x-model.number="combination.price" :placeholder="basePrice"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Stok</label>
                                    <input type="number" min="0" :name="'variants[' + index + '][stock]'" x-model.number="combination.stock" required
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        <div x-show="variantTypes.length > 0 && combinations.length === 0" x-cloak class="rounded-2xl border border-yellow-200 bg-yellow-50 p-5">
            <div class="flex gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.71 2.14h17.64a2 2 0 001.71-2.14l-8.82-15a2 2 0 00-3.42 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-yellow-800">Kombinasi belum terbentuk</p>
                    <p class="text-sm text-yellow-700 mt-1">Pastikan setiap variant memiliki nama dan minimal satu nilai.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" :disabled="submitting"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg x-show="submitting" x-cloak class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Produk'"></span>
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
function productCreateForm() {
    return {
        submitting: false,
        mainImagePreview: null,
        baseSku: '{{ old('sku') }}',
        basePrice: {{ (int) old('price', 0) }},

        variantTypes: @js(
            old('variant_groups', [
                ['name' => '', 'values' => [['name' => '', 'image' => null]]],
            ])
        ).map((variant, index) => {
            return {
                id: Date.now() + index,
                name: variant.name || '',
                values: Array.isArray(variant.values) && variant.values.length
                    ? variant.values.map((value, valueIndex) => {
                        return {
                            id: Date.now() + index + valueIndex + Math.random(),
                            name: typeof value === 'object' ? (value.name || '') : (value || ''),
                            image: null,
                            preview: typeof value === 'object' ? (value.image || null) : null
                        };
                    })
                    : [{ id: Date.now() + Math.random(), name: '', image: null, preview: null }]
            };
        }),

        combinations: [],

        init() {
            this.refreshCombinations();
        },

        previewMainImage(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) { this.mainImagePreview = null; return; }
            this.mainImagePreview = URL.createObjectURL(file);
        },

        previewVariantImage(event, variantIndex, valueIndex) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                this.variantTypes[variantIndex].values[valueIndex].image = null;
                this.variantTypes[variantIndex].values[valueIndex].preview = null;
                return;
            }
            this.variantTypes[variantIndex].values[valueIndex].image = file;
            this.variantTypes[variantIndex].values[valueIndex].preview = URL.createObjectURL(file);
        },

        addVariantType() {
            const index = this.variantTypes.length;
            this.variantTypes.push({
                id: Date.now() + Math.random(),
                name: index === 0 ? 'Warna' : index === 1 ? 'Ukuran' : '',
                values: [{ id: Date.now() + Math.random(), name: '', image: null, preview: null }]
            });
            this.refreshCombinations();
            this.$nextTick(() => {
                const inputs = document.querySelectorAll('input[name^="variant_groups"][name$="[name]"]');
                const lastInput = inputs[inputs.length - 1];
                if (lastInput) lastInput.focus();
            });
        },

        removeVariantType(index) {
            this.variantTypes.splice(index, 1);
            this.refreshCombinations();
        },

        addVariantValue(variantIndex) {
            this.variantTypes[variantIndex].values.push({ id: Date.now() + Math.random(), name: '', image: null, preview: null });
            this.refreshCombinations();
        },

        removeVariantValue(variantIndex, valueIndex) {
            const values = this.variantTypes[variantIndex].values;
            if (values.length <= 1) {
                values[0].name = '';
                values[0].image = null;
                values[0].preview = null;
                this.refreshCombinations();
                return;
            }
            values.splice(valueIndex, 1);
            this.refreshCombinations();
        },

        refreshCombinations() {
            const validVariants = this.variantTypes.filter(variant => {
                return variant.name && variant.name.trim() !== '' &&
                    Array.isArray(variant.values) &&
                    variant.values.some(value => value.name && value.name.trim() !== '');
            });

            if (!validVariants.length) { this.combinations = []; return; }

            const valueGroups = validVariants.map(variant => {
                return variant.values
                    .filter(value => value.name && value.name.trim() !== '')
                    .map(value => value.name.trim());
            });

            if (valueGroups.some(group => group.length === 0)) { this.combinations = []; return; }

            const cartesian = (arrays) => {
                return arrays.reduce((acc, current) => {
                    const result = [];
                    acc.forEach(combination => {
                        current.forEach(value => { result.push([...combination, value]); });
                    });
                    return result;
                }, [[]]);
            };

            const generated = cartesian(valueGroups);
            const oldCombinations = this.combinations || [];

            this.combinations = generated.map(attributes => {
                const existing = oldCombinations.find(item => JSON.stringify(item.attributes) === JSON.stringify(attributes));
                return {
                    id: existing?.id || Date.now() + Math.random(),
                    attributes,
                    sku_variant: existing?.sku_variant || this.generateSku(attributes),
                    price: existing?.price ?? null,
                    stock: existing?.stock ?? 0
                };
            });
        },

        generateSku(attributes) {
            const values = attributes
                .filter(value => value && value.trim() !== '')
                .map(value => value.trim().toUpperCase().replace(/[^A-Z0-9]+/g, '-').replace(/^-+|-+$/g, ''));
            const suffix = values.join('-');
            const base = (this.baseSku || '').trim();
            if (base && suffix) return base + '-' + suffix;
            return suffix || base;
        },

        prepareSubmit() {
            this.submitting = true;
            this.combinations.forEach((combination) => {
                combination.stock = Number(combination.stock || 0);
                if (!combination.sku_variant || !combination.sku_variant.trim()) {
                    combination.sku_variant = this.generateSku(combination.attributes);
                }
            });
        }
    };
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
