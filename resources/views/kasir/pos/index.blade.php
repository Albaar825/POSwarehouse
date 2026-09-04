@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')

<div
    x-data="posApp()"
    class="space-y-5"
>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

        <div>
            <h2 class="text-xl font-bold text-gray-900">
                Point of Sale
            </h2>

            <p class="text-sm text-gray-500">
                Kelola transaksi penjualan
            </p>
        </div>

        <div class="text-sm text-gray-500">
            Kasir:
            <span class="font-semibold text-gray-800">
                {{ auth()->user()->name }}
            </span>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SEARCH --}}
    {{-- ========================================================= --}}

    <div class="bg-white border border-gray-200 rounded-xl p-4">

        <div class="relative">

            <svg
                class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"
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
                x-model="search"
                placeholder="Cari nama produk atau SKU..."
                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none"
            >

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MAIN GRID --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">


        {{-- ===================================================== --}}
        {{-- PRODUK --}}
        {{-- ===================================================== --}}

        <div class="xl:col-span-2">

            <div class="bg-white border border-gray-200 rounded-xl">

                <div class="p-4 border-b border-gray-200">

                    <div class="flex items-center justify-between">

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Produk
                            </h3>

                            <p class="text-xs text-gray-500">
                                Pilih produk yang ingin dijual
                            </p>

                        </div>

                        <span class="text-xs text-gray-500">
                            {{ $products->count() }} produk
                        </span>

                    </div>

                </div>


                <div class="p-4">

                    @if ($products->count())

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

                            @foreach ($products as $product)

                                <button
                                    type="button"
                                    @click="openVariant({{ $product->id }})"
                                    x-show="matches(
                                        '{{ strtolower(addslashes($product->name)) }}',
                                        '{{ strtolower(addslashes($product->sku)) }}'
                                    )"
                                    class="text-left border border-gray-200 rounded-xl overflow-hidden hover:border-gray-400 hover:shadow-sm transition bg-white"
                                >

                                    {{-- PRODUCT IMAGE --}}

                                    <div class="aspect-square bg-gray-100 overflow-hidden">

                                        @php
                                            $productJson = $productsJson->firstWhere('id', $product->id);
                                            $productImage = $productJson['image'] ?? null;
                                        @endphp

                                        @if ($productImage)

                                            <img
                                                src="{{ $productImage }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover"
                                            >

                                        @else

                                            <div class="w-full h-full flex items-center justify-center text-gray-400">

                                                <svg
                                                    class="w-10 h-10"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M4 16l4-4a3 3 0 014 0l2 2 1-1a3 3 0 014 0l1 1M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v14a1 1 0 001 1z"
                                                    />
                                                </svg>

                                            </div>

                                        @endif

                                    </div>


                                    {{-- PRODUCT INFO --}}

                                    <div class="p-3">

                                        <p class="font-semibold text-sm text-gray-900 line-clamp-2">
                                            {{ $product->name }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $product->sku }}
                                        </p>

                                        <p class="font-bold text-sm text-gray-900 mt-2">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>

                                        <p class="text-xs text-green-600 mt-1">
                                            Stok {{ $product->stock }} {{ $product->unit }}
                                        </p>

                                    </div>

                                </button>

                            @endforeach

                        </div>

                    @else

                        <div class="py-12 text-center text-gray-500">
                            Tidak ada produk tersedia.
                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- KERANJANG --}}
        {{-- ===================================================== --}}

        <div>

            <div class="bg-white border border-gray-200 rounded-xl sticky top-20">


                {{-- CART HEADER --}}

                <div class="p-4 border-b border-gray-200">

                    <div class="flex items-center justify-between">

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Keranjang
                            </h3>

                            <p class="text-xs text-gray-500">
                                <span x-text="cart.length"></span> item
                            </p>

                        </div>

                        <button
                            type="button"
                            x-show="cart.length"
                            @click="clearCart()"
                            class="text-xs text-red-600 hover:text-red-700"
                        >
                            Kosongkan
                        </button>

                    </div>

                </div>


                {{-- CART ITEMS --}}

                <div class="max-h-[420px] overflow-y-auto">

                    <template x-if="cart.length === 0">

                        <div class="py-14 text-center px-5">

                            <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">

                                <svg
                                    class="w-7 h-7 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 2h13m-5 4a1 1 0 1 1-2 0m8 0a1 1 0 1 1-2 0"
                                    />
                                </svg>

                            </div>

                            <p class="mt-3 text-sm text-gray-500">
                                Keranjang masih kosong
                            </p>

                        </div>

                    </template>


                    <div class="divide-y divide-gray-100">

                        <template
                            x-for="(item, index) in cart"
                            :key="item.key"
                        >

                            <div class="p-4">

                                <div class="flex gap-3">

                                    <img
                                        :src="item.image"
                                        class="w-14 h-14 rounded-lg object-cover bg-gray-100"
                                        alt=""
                                    >

                                    <div class="flex-1 min-w-0">

                                        <p
                                            class="font-semibold text-sm text-gray-900 truncate"
                                            x-text="item.name"
                                        ></p>

                                        <p
                                            class="text-xs text-gray-500 mt-1"
                                            x-text="item.variant"
                                        ></p>

                                        <p
                                            class="text-xs text-gray-500"
                                            x-text="formatRupiah(item.price)"
                                        ></p>

                                    </div>

                                    <button
                                        type="button"
                                        @click="removeItem(index)"
                                        class="text-gray-400 hover:text-red-500"
                                    >
                                        ×
                                    </button>

                                </div>


                                <div class="flex items-center justify-between mt-3">

                                    <div class="flex items-center border border-gray-200 rounded-lg">

                                        <button
                                            type="button"
                                            @click="decrease(index)"
                                            class="w-8 h-8 text-gray-600 hover:bg-gray-100"
                                        >
                                            −
                                        </button>

                                        <span
                                            class="w-8 text-center text-sm font-semibold"
                                            x-text="item.quantity"
                                        ></span>

                                        <button
                                            type="button"
                                            @click="increase(index)"
                                            class="w-8 h-8 text-gray-600 hover:bg-gray-100"
                                        >
                                            +
                                        </button>

                                    </div>

                                    <p
                                        class="font-bold text-sm"
                                        x-text="formatRupiah(item.price * item.quantity)"
                                    ></p>

                                </div>

                            </div>

                        </template>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- CHECKOUT --}}
                {{-- ================================================= --}}

                <div class="p-4 border-t border-gray-200 space-y-4">


                    {{-- TOTAL --}}

                    <div class="flex items-center justify-between">

                        <span class="text-sm text-gray-500">
                            Total
                        </span>

                        <span
                            class="text-xl font-bold text-gray-900"
                            x-text="formatRupiah(total)"
                        ></span>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('pos.store') }}"
                        @submit="prepareSubmit($event)"
                    >

                        @csrf

                        <div id="checkout-items"></div>


                        {{-- PAYMENT METHOD --}}

                        <div class="space-y-2">

                            <label class="text-sm font-medium text-gray-700">
                                Metode Pembayaran
                            </label>

                            <div class="grid grid-cols-3 gap-2">


                                {{-- CASH --}}

                                <button
                                    type="button"
                                    @click="paymentMethod = 'cash'; paid = 0"
                                    :class="paymentMethod === 'cash'
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200'"
                                    class="border rounded-lg py-2.5 text-sm font-medium"
                                >
                                    Cash
                                </button>


                                {{-- QRIS --}}

                                <button
                                    type="button"
                                    @click="paymentMethod = 'qris'; paid = total"
                                    :class="paymentMethod === 'qris'
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200'"
                                    class="border rounded-lg py-2.5 text-sm font-medium"
                                >
                                    QRIS
                                </button>


                                {{-- CREDIT --}}

                                <button
                                    type="button"
                                    @click="paymentMethod = 'credit'; paid = 0"
                                    :class="paymentMethod === 'credit'
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200'"
                                    class="border rounded-lg py-2.5 text-sm font-medium"
                                >
                                    Kredit
                                </button>

                            </div>

                            <input
                                type="hidden"
                                name="payment_method"
                                :value="paymentMethod"
                            >

                        </div>


                        {{-- ================================================= --}}
                        {{-- CUSTOMER CREDIT --}}
                        {{-- ================================================= --}}

                        <div
                            x-show="paymentMethod === 'credit'"
                            x-cloak
                            class="space-y-3"
                        >

                            {{-- NAMA --}}

                            <div class="space-y-2">

                                <label class="text-sm font-medium text-gray-700">
                                    Nama Customer
                                </label>

                                <input
                                    type="text"
                                    name="customer_name"
                                    x-model="customerName"
                                    :required="paymentMethod === 'credit'"
                                    class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                    placeholder="Masukkan nama customer"
                                >

                            </div>


                            {{-- NOMOR HP --}}

                            <div class="space-y-2">

                                <label class="text-sm font-medium text-gray-700">
                                    No. HP Customer
                                </label>

                                <input
                                    type="text"
                                    name="customer_phone"
                                    x-model="customerPhone"
                                    :required="paymentMethod === 'credit'"
                                    class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                    placeholder="Masukkan nomor HP customer"
                                >

                            </div>


                            {{-- JATUH TEMPO --}}

                            <div class="space-y-2">

                                <label class="text-sm font-medium text-gray-700">
                                    Jatuh Tempo
                                </label>

                                <input
                                    type="date"
                                    name="due_date"
                                    x-model="dueDate"
                                    :required="paymentMethod === 'credit'"
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                >

                            </div>


                            {{-- INFO --}}

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-3">

                                <p class="text-sm font-medium text-yellow-800">
                                    Transaksi Kredit
                                </p>

                                <p class="text-xs text-yellow-700 mt-1">
                                    Barang akan langsung mengurangi stok dan pembayaran dapat dilakukan kemudian.
                                </p>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- PAID --}}
                        {{-- ================================================= --}}

                        <div
                            class="space-y-2"
                            x-show="paymentMethod !== 'credit'"
                        >

                            <label class="text-sm font-medium text-gray-700">
                                Uang Dibayar
                            </label>

                            <input
                                type="number"
                                x-model.number="paid"
                                name="paid"
                                min="0"
                                :readonly="paymentMethod === 'qris'"
                                :value="paymentMethod === 'qris' ? total : paid"
                                class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                placeholder="Masukkan nominal"
                            >

                        </div>


                        {{-- ================================================= --}}
                        {{-- CHANGE --}}
                        {{-- ================================================= --}}

                        <div
                            x-show="
                                paymentMethod !== 'credit' &&
                                paid >= total &&
                                total > 0
                            "
                            class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-3"
                        >

                            <span class="text-sm text-green-700">
                                Kembalian
                            </span>

                            <span
                                class="font-bold text-green-700"
                                x-text="formatRupiah(Math.max(0, paid - total))"
                            ></span>

                        </div>


                        {{-- ================================================= --}}
                        {{-- CREDIT SUMMARY --}}
                        {{-- ================================================= --}}

                        <div
                            x-show="
                                paymentMethod === 'credit' &&
                                total > 0
                            "
                            class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-3"
                        >

                            <span class="text-sm text-yellow-700">
                                Sisa Tagihan
                            </span>

                            <span
                                class="font-bold text-yellow-700"
                                x-text="formatRupiah(total)"
                            ></span>

                        </div>


                        {{-- ================================================= --}}
                        {{-- SUBMIT --}}
                        {{-- ================================================= --}}

                        <button
                            type="submit"
                            :disabled="
                                cart.length === 0 ||

                                (
                                    paymentMethod !== 'credit' &&
                                    paid < total
                                ) ||

                                (
                                    paymentMethod === 'credit' &&
                                    (
                                        !customerName ||
                                        !customerPhone ||
                                        !dueDate
                                    )
                                )
                            "
                            class="w-full mt-3 bg-gray-900 text-white py-3 rounded-lg font-semibold hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                        >

                            <span x-show="paymentMethod !== 'credit'">
                                Bayar Sekarang
                            </span>

                            <span x-show="paymentMethod === 'credit'">
                                Simpan Transaksi Kredit
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- VARIANT MODAL --}}
    {{-- ========================================================= --}}

    <div
        x-show="variantModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >

        {{-- OVERLAY --}}

        <div
            class="absolute inset-0 bg-black/50"
            @click="closeVariantModal()"
        ></div>


        {{-- MODAL --}}

        <div
            class="relative bg-white rounded-2xl w-full max-w-md shadow-xl max-h-[90vh] overflow-y-auto"
        >

            {{-- ================================================= --}}
            {{-- HEADER --}}
            {{-- ================================================= --}}

            <div class="p-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="font-bold text-lg">
                            Pilih Varian
                        </h3>

                        <p
                            class="text-sm text-gray-500 mt-1"
                            x-text="selectedProduct ? selectedProduct.name : ''"
                        ></p>

                    </div>

                    <button
                        type="button"
                        @click="closeVariantModal()"
                        class="text-gray-400 hover:text-gray-700 text-2xl"
                    >
                        ×
                    </button>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- IMAGE GALLERY --}}
            {{-- ================================================= --}}

            <div class="px-5 pt-5">

                <div class="relative">

                    <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">

                        <template x-if="currentImages.length > 0">

                            <img
                                :src="currentImages[currentImageIndex]"
                                class="w-full h-full object-cover"
                                alt=""
                            >

                        </template>

                        <template x-if="currentImages.length === 0">

                            <div class="w-full h-full flex items-center justify-center text-gray-400">

                                <svg
                                    class="w-12 h-12"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M4 16l4-4a3 3 0 014 0l2 2 1-1a3 3 0 014 0l1 1M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v14a1 1 0 001 1z"
                                    />
                                </svg>

                            </div>

                        </template>

                    </div>


                    {{-- PREVIOUS --}}

                    <button
                        type="button"
                        x-show="currentImages.length > 1"
                        @click="previousImage()"
                        class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center hover:bg-white transition"
                    >
                        ‹
                    </button>


                    {{-- NEXT --}}

                    <button
                        type="button"
                        x-show="currentImages.length > 1"
                        @click="nextImage()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center hover:bg-white transition"
                    >
                        ›
                    </button>

                </div>


                {{-- THUMBNAILS --}}

                <div
                    x-show="currentImages.length > 1"
                    class="flex gap-2 mt-3 overflow-x-auto pb-1"
                >

                    <template
                        x-for="(image, index) in currentImages"
                        :key="image + index"
                    >

                        <button
                            type="button"
                            @click="currentImageIndex = index"
                            class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 transition"
                            :class="
                                currentImageIndex === index
                                    ? 'border-gray-900'
                                    : 'border-gray-200'
                            "
                        >

                            <img
                                :src="image"
                                class="w-full h-full object-cover"
                                alt=""
                            >

                        </button>

                    </template>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- CONTENT --}}
            {{-- ================================================= --}}

            <div class="p-5 space-y-5">


                {{-- ================================================= --}}
                {{-- WARNA --}}
                {{-- ================================================= --}}

                <div>

                    <div class="flex items-center justify-between mb-2">

                        <label class="text-sm font-semibold text-gray-900">
                            Warna
                        </label>

                        <span
                            x-show="selectedColor"
                            class="text-xs text-gray-500"
                            x-text="selectedColor"
                        ></span>

                    </div>


                    <div class="flex flex-wrap gap-2">

                        <template
                            x-for="color in availableColors"
                            :key="color"
                        >

                            <button
                                type="button"
                                @click="selectColor(color)"
                                class="px-4 py-2.5 rounded-lg border text-sm font-medium transition"
                                :class="
                                    selectedColor === color
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200 hover:border-gray-900'
                                "
                                x-text="color"
                            ></button>

                        </template>


                        <template x-if="availableColors.length === 0">

                            <span class="text-sm text-gray-500">
                                Tidak ada pilihan warna.
                            </span>

                        </template>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- SIZE --}}
                {{-- ================================================= --}}

                <div>

                    <div class="flex items-center justify-between mb-2">

                        <label class="text-sm font-semibold text-gray-900">
                            Ukuran
                        </label>

                        <span
                            x-show="selectedSize"
                            class="text-xs text-gray-500"
                            x-text="selectedSize"
                        ></span>

                    </div>


                    <div class="grid grid-cols-4 gap-2">

                        <template
                            x-for="size in availableSizes"
                            :key="size"
                        >

                            <button
                                type="button"
                                @click="selectSize(size)"
                                :disabled="sizeStock(size) <= 0"
                                class="py-2.5 rounded-lg border text-sm font-medium transition"
                                :class="
                                    selectedSize === size
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : sizeStock(size) > 0
                                            ? 'bg-white text-gray-700 border-gray-200 hover:border-gray-900'
                                            : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                                "
                                x-text="size"
                            ></button>

                        </template>


                        <template x-if="availableSizes.length === 0">

                            <div class="col-span-4 py-3 text-center text-sm text-gray-500">
                                Pilih warna terlebih dahulu.
                            </div>

                        </template>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- DETAIL VARIANT --}}
                {{-- ================================================= --}}

                <template x-if="selectedVariant">

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">

                        <div class="flex items-center justify-between">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Varian dipilih
                                </p>

                                <p
                                    class="font-semibold text-gray-900 mt-1"
                                    x-text="selectedVariant.label"
                                ></p>

                            </div>

                            <div class="text-right">

                                <p class="text-xs text-gray-500">
                                    Stok
                                </p>

                                <p
                                    class="font-bold text-green-600 mt-1"
                                    x-text="selectedVariant.stock"
                                ></p>

                            </div>

                        </div>


                        <div class="mt-3 pt-3 border-t border-gray-200">

                            <p class="text-xs text-gray-500">
                                SKU Variant
                            </p>

                            <p
                                class="text-sm font-medium text-gray-800 mt-1"
                                x-text="selectedVariant.sku_variant || '-'"
                            ></p>

                        </div>

                    </div>

                </template>


                {{-- ================================================= --}}
                {{-- ADD TO CART --}}
                {{-- ================================================= --}}

                <button
                    type="button"
                    @click="addSelectedVariant()"
                    :disabled="
                        !selectedVariant ||
                        Number(selectedVariant.stock) <= 0
                    "
                    class="w-full bg-gray-900 text-white py-3 rounded-lg font-semibold hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                >

                    <span x-show="!selectedVariant">
                        Pilih Warna & Ukuran
                    </span>

                    <span x-show="selectedVariant">
                        Tambah ke Keranjang
                    </span>

                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script
    src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
    defer
></script>


<script>

function posApp() {

    return {

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        search: '',


        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        cart: [],


        /*
        |--------------------------------------------------------------------------
        | VARIANT MODAL
        |--------------------------------------------------------------------------
        */

        variantModal: false,

        selectedProduct: null,

        selectedColor: null,

        selectedSize: null,

        selectedVariant: null,


        /*
        |--------------------------------------------------------------------------
        | IMAGE GALLERY
        |--------------------------------------------------------------------------
        */

        currentImages: [],

        currentImageIndex: 0,


        /*
        |--------------------------------------------------------------------------
        | PAYMENT
        |--------------------------------------------------------------------------
        */

        paymentMethod: 'cash',

        paid: 0,

        customerName: '',

        customerPhone: '',

        dueDate: '',


        /*
        |--------------------------------------------------------------------------
        | PRODUCT DATA
        |--------------------------------------------------------------------------
        */

        products: @js($productsJson),


        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        get total() {

            return this.cart.reduce(
                (sum, item) => {

                    return sum +
                        (
                            Number(item.price) *
                            Number(item.quantity)
                        );

                },
                0
            );

        },


        /*
        |--------------------------------------------------------------------------
        | AVAILABLE COLORS
        |--------------------------------------------------------------------------
        */

        get availableColors() {

            if (!this.selectedProduct) {
                return [];
            }

            return [
                ...new Set(
                    this.selectedProduct.variants
                        .filter(
                            variant =>
                                Number(variant.stock) > 0
                        )
                        .map(
                            variant =>
                                variant.color
                        )
                        .filter(
                            color =>
                                color
                        )
                )
            ];

        },


        /*
        |--------------------------------------------------------------------------
        | AVAILABLE SIZES
        |--------------------------------------------------------------------------
        */

        get availableSizes() {

            if (
                !this.selectedProduct ||
                !this.selectedColor
            ) {
                return [];
            }

            const selectedColorKey =
                String(this.selectedColor)
                    .toLowerCase()
                    .trim();

            return [
                ...new Set(
                    this.selectedProduct.variants
                        .filter(variant => {

                            return String(
                                variant.color
                            )
                                .toLowerCase()
                                .trim() ===
                                selectedColorKey;

                        })
                        .map(
                            variant =>
                                variant.size
                        )
                        .filter(
                            size =>
                                size
                        )
                )
            ];

        },


        /*
        |--------------------------------------------------------------------------
        | SEARCH MATCH
        |--------------------------------------------------------------------------
        */

        matches(name, sku) {

            const keyword =
                this.search
                    .toLowerCase()
                    .trim();

            if (!keyword) {
                return true;
            }

            return (
                name.includes(keyword) ||
                sku.includes(keyword)
            );

        },


        /*
        |--------------------------------------------------------------------------
        | OPEN VARIANT
        |--------------------------------------------------------------------------
        */

        openVariant(productId) {

            this.selectedProduct =
                this.products.find(
                    product =>
                        Number(product.id) ===
                        Number(productId)
                );


            if (!this.selectedProduct) {

                console.error(
                    'Produk tidak ditemukan:',
                    productId
                );

                return;
            }


            if (
                !Array.isArray(
                    this.selectedProduct.variants
                )
            ) {

                this.selectedProduct.variants = [];

            }


            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            this.selectedColor = null;

            this.selectedSize = null;

            this.selectedVariant = null;

            this.currentImageIndex = 0;


            /*
            |--------------------------------------------------------------------------
            | DEFAULT IMAGE
            |--------------------------------------------------------------------------
            */

            this.currentImages =
                this.selectedProduct.image
                    ? [this.selectedProduct.image]
                    : [];


            /*
            |--------------------------------------------------------------------------
            | OPEN MODAL
            |--------------------------------------------------------------------------
            */

            this.variantModal = true;

        },


        /*
        |--------------------------------------------------------------------------
        | SELECT COLOR
        |--------------------------------------------------------------------------
        */

        selectColor(color) {

            this.selectedColor = color;

            this.selectedSize = null;

            this.selectedVariant = null;

            this.currentImageIndex = 0;


            /*
            |--------------------------------------------------------------------------
            | COLOR KEY
            |--------------------------------------------------------------------------
            */

            const colorKey =
                String(color)
                    .toLowerCase()
                    .trim();


            let images = [];


            /*
            |--------------------------------------------------------------------------
            | PRIORITAS 1
            | color_images dari controller
            |--------------------------------------------------------------------------
            */

            if (
                this.selectedProduct.color_images &&
                Array.isArray(
                    this.selectedProduct.color_images[colorKey]
                )
            ) {

                images =
                    this.selectedProduct.color_images[colorKey];

            }


            /*
            |--------------------------------------------------------------------------
            | PRIORITAS 2
            | Gabungkan gambar semua variant
            | dengan warna yang sama
            |--------------------------------------------------------------------------
            */

            if (
                !Array.isArray(images) ||
                images.length === 0
            ) {

                images =
                    this.selectedProduct.variants
                        .filter(variant => {

                            return String(
                                variant.color
                            )
                                .toLowerCase()
                                .trim() ===
                                colorKey;

                        })
                        .flatMap(variant => {

                            return Array.isArray(
                                variant.images
                            )
                                ? variant.images
                                : [];

                        });

            }


            /*
            |--------------------------------------------------------------------------
            | HILANGKAN DUPLIKAT
            |--------------------------------------------------------------------------
            */

            this.currentImages = [
                ...new Set(
                    Array.isArray(images)
                        ? images
                        : []
                )
            ];


            /*
            |--------------------------------------------------------------------------
            | FALLBACK
            |--------------------------------------------------------------------------
            */

            if (
                this.currentImages.length === 0 &&
                this.selectedProduct.image
            ) {

                this.currentImages = [
                    this.selectedProduct.image
                ];

            }


            this.currentImageIndex = 0;

        },


        /*
        |--------------------------------------------------------------------------
        | SELECT SIZE
        |--------------------------------------------------------------------------
        |
        | PENTING:
        | Jangan mengubah currentImages di sini.
        |
        | Gallery tetap berdasarkan warna.
        |
        */

        selectSize(size) {

            if (!this.selectedColor) {
                return;
            }


            const colorKey =
                String(this.selectedColor)
                    .toLowerCase()
                    .trim();

            const sizeKey =
                String(size)
                    .toLowerCase()
                    .trim();


            /*
            |--------------------------------------------------------------------------
            | CARI EXACT VARIANT
            |--------------------------------------------------------------------------
            */

            const variant =
                this.selectedProduct.variants.find(
                    variant => {

                        const variantColor =
                            String(
                                variant.color
                            )
                                .toLowerCase()
                                .trim();

                        const variantSize =
                            String(
                                variant.size
                            )
                                .toLowerCase()
                                .trim();

                        return (
                            variantColor === colorKey &&
                            variantSize === sizeKey
                        );

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | VARIANT TIDAK DITEMUKAN
            |--------------------------------------------------------------------------
            */

            if (!variant) {

                this.selectedVariant = null;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | STOK HABIS
            |--------------------------------------------------------------------------
            */

            if (
                Number(variant.stock) <= 0
            ) {

                this.selectedVariant = null;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | SET VARIANT
            |--------------------------------------------------------------------------
            */

            this.selectedSize = size;

            this.selectedVariant = variant;


            /*
            |--------------------------------------------------------------------------
            | JANGAN UBAH GALLERY
            |--------------------------------------------------------------------------
            |
            | Gallery tetap menggunakan gambar berdasarkan
            | warna yang dipilih.
            |
            */

        },


        /*
        |--------------------------------------------------------------------------
        | CEK STOK SIZE
        |--------------------------------------------------------------------------
        */

        sizeStock(size) {

            if (
                !this.selectedProduct ||
                !this.selectedColor
            ) {

                return 0;
            }


            const colorKey =
                String(this.selectedColor)
                    .toLowerCase()
                    .trim();

            const sizeKey =
                String(size)
                    .toLowerCase()
                    .trim();


            const variant =
                this.selectedProduct.variants.find(
                    variant => {

                        return (
                            String(
                                variant.color
                            )
                                .toLowerCase()
                                .trim() ===
                            colorKey &&

                            String(
                                variant.size
                            )
                                .toLowerCase()
                                .trim() ===
                            sizeKey
                        );

                    }
                );


            return variant
                ? Number(variant.stock)
                : 0;

        },


        /*
        |--------------------------------------------------------------------------
        | ADD SELECTED VARIANT
        |--------------------------------------------------------------------------
        */

        addSelectedVariant() {

            if (!this.selectedProduct) {
                return;
            }


            if (!this.selectedVariant) {

                alert(
                    'Silakan pilih warna dan ukuran terlebih dahulu.'
                );

                return;
            }


            this.addToCart(
                this.selectedVariant
            );

        },


        /*
        |--------------------------------------------------------------------------
        | ADD TO CART
        |--------------------------------------------------------------------------
        */

        addToCart(variant) {

            if (
                !this.selectedProduct ||
                !variant
            ) {

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK STOK
            |--------------------------------------------------------------------------
            */

            if (
                Number(variant.stock) <= 0
            ) {

                alert(
                    'Stok varian habis.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CART KEY
            |--------------------------------------------------------------------------
            |
            | Product + Variant.
            |
            */

            const key =
                this.selectedProduct.id +
                '-' +
                variant.id;


            const existing =
                this.cart.find(
                    item =>
                        item.key === key
                );


            /*
            |--------------------------------------------------------------------------
            | ITEM SUDAH ADA
            |--------------------------------------------------------------------------
            */

            if (existing) {

                if (
                    existing.quantity <
                    Number(variant.stock)
                ) {

                    existing.quantity++;

                } else {

                    alert(
                        'Jumlah melebihi stok yang tersedia.'
                    );

                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | GAMBAR CART
                |--------------------------------------------------------------------------
                */

                let image =
                    this.selectedProduct.image;


                /*
                | Ambil gambar pertama dari warna
                | agar konsisten dengan gallery.
                */

                const colorKey =
                    String(
                        variant.color ?? ''
                    )
                        .toLowerCase()
                        .trim();


                if (
                    this.selectedProduct.color_images &&
                    Array.isArray(
                        this.selectedProduct.color_images[colorKey]
                    ) &&
                    this.selectedProduct.color_images[colorKey].length
                ) {

                    image =
                        this.selectedProduct
                            .color_images[colorKey][0];

                } else if (
                    Array.isArray(
                        variant.images
                    ) &&
                    variant.images.length > 0
                ) {

                    image =
                        variant.images[0];

                }


                /*
                |--------------------------------------------------------------------------
                | PUSH CART
                |--------------------------------------------------------------------------
                */

                this.cart.push({

                    key: key,

                    product_id:
                        this.selectedProduct.id,

                    product_variant_id:
                        variant.id,

                    name:
                        this.selectedProduct.name,

                    variant:
                        variant.label,

                    color:
                        variant.color,

                    size:
                        variant.size,

                    price:
                        Number(
                            this.selectedProduct.price
                        ),

                    image:
                        image,

                    stock:
                        Number(
                            variant.stock
                        ),

                    quantity: 1,

                });

            }


            /*
            |--------------------------------------------------------------------------
            | CLOSE MODAL
            |--------------------------------------------------------------------------
            */

            this.closeVariantModal();

        },


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        closeVariantModal() {

            this.variantModal = false;

            this.selectedProduct = null;

            this.selectedColor = null;

            this.selectedSize = null;

            this.selectedVariant = null;

            this.currentImages = [];

            this.currentImageIndex = 0;

        },


        /*
        |--------------------------------------------------------------------------
        | NEXT IMAGE
        |--------------------------------------------------------------------------
        */

        nextImage() {

            if (
                this.currentImages.length <= 1
            ) {

                return;
            }


            this.currentImageIndex =
                (
                    this.currentImageIndex + 1
                ) %
                this.currentImages.length;

        },


        /*
        |--------------------------------------------------------------------------
        | PREVIOUS IMAGE
        |--------------------------------------------------------------------------
        */

        previousImage() {

            if (
                this.currentImages.length <= 1
            ) {

                return;
            }


            this.currentImageIndex =
                (
                    this.currentImageIndex -
                    1 +
                    this.currentImages.length
                ) %
                this.currentImages.length;

        },


        /*
        |--------------------------------------------------------------------------
        | INCREASE
        |--------------------------------------------------------------------------
        */

        increase(index) {

            const item =
                this.cart[index];


            if (
                item.quantity <
                Number(item.stock)
            ) {

                item.quantity++;

            } else {

                alert(
                    'Jumlah melebihi stok yang tersedia.'
                );

            }

        },


        /*
        |--------------------------------------------------------------------------
        | DECREASE
        |--------------------------------------------------------------------------
        */

        decrease(index) {

            const item =
                this.cart[index];


            if (
                item.quantity > 1
            ) {

                item.quantity--;

            } else {

                this.removeItem(index);

            }

        },


        /*
        |--------------------------------------------------------------------------
        | REMOVE
        |--------------------------------------------------------------------------
        */

        removeItem(index) {

            this.cart.splice(
                index,
                1
            );

        },


        /*
        |--------------------------------------------------------------------------
        | CLEAR CART
        |--------------------------------------------------------------------------
        */

        clearCart() {

            this.cart = [];

            this.paid = 0;

            this.customerName = '';

            this.customerPhone = '';

            this.dueDate = '';

        },


        /*
        |--------------------------------------------------------------------------
        | FORMAT RUPIAH
        |--------------------------------------------------------------------------
        */

        formatRupiah(value) {

            return new Intl.NumberFormat(
                'id-ID',
                {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }
            ).format(
                value || 0
            );

        },


        /*
        |--------------------------------------------------------------------------
        | PREPARE SUBMIT
        |--------------------------------------------------------------------------
        */

        prepareSubmit(event) {

            /*
            |--------------------------------------------------------------------------
            | CART KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                this.cart.length === 0
            ) {

                event.preventDefault();

                alert(
                    'Keranjang masih kosong.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CASH / QRIS
            |--------------------------------------------------------------------------
            */

            if (
                this.paymentMethod !== 'credit' &&
                this.paid < this.total
            ) {

                event.preventDefault();

                alert(
                    'Uang pembayaran kurang.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CREDIT
            |--------------------------------------------------------------------------
            */

            if (
                this.paymentMethod === 'credit'
            ) {

                if (
                    !this.customerName.trim()
                ) {

                    event.preventDefault();

                    alert(
                        'Silakan masukkan nama customer.'
                    );

                    return;
                }


                if (
                    !this.customerPhone.trim()
                ) {

                    event.preventDefault();

                    alert(
                        'Silakan masukkan nomor HP customer.'
                    );

                    return;
                }


                if (
                    !this.dueDate
                ) {

                    event.preventDefault();

                    alert(
                        'Silakan tentukan tanggal jatuh tempo.'
                    );

                    return;
                }


                this.paid = 0;

            }


            /*
            |--------------------------------------------------------------------------
            | CHECKOUT ITEMS
            |--------------------------------------------------------------------------
            */

            const container =
                document.getElementById(
                    'checkout-items'
                );


            container.innerHTML = '';


            /*
            |--------------------------------------------------------------------------
            | GENERATE INPUT
            |--------------------------------------------------------------------------
            */

            this.cart.forEach(
                (item, index) => {

                    container.innerHTML += `

                        <input
                            type="hidden"
                            name="items[${index}][product_id]"
                            value="${item.product_id}"
                        >

                        <input
                            type="hidden"
                            name="items[${index}][product_variant_id]"
                            value="${item.product_variant_id}"
                        >

                        <input
                            type="hidden"
                            name="items[${index}][quantity]"
                            value="${item.quantity}"
                        >

                    `;

                }
            );

        }

    };

}

</script>


<style>

[x-cloak] {
    display: none !important;
}

</style>

@endpush
