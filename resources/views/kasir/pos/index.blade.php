@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')

<div x-data="posApp()" class="space-y-5">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Point of Sale</h2>
            <p class="text-sm text-gray-500">Kelola transaksi penjualan</p>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                @click="openOpenInvoiceModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:border-gray-900 hover:text-gray-900 transition"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 14.25l6-6m-5.25-3h6.5A2.75 2.75 0 0119 8v8a2.75 2.75 0 01-2.75 2.75h-8.5A2.75 2.75 0 015 16V8a2.75 2.75 0 012.75-2.75h1.5"/>
                </svg>

                Open Invoice

                <span
                    x-show="openInvoices.length > 0"
                    x-cloak
                    class="min-w-5 h-5 px-1.5 rounded-full bg-gray-900 text-white text-xs flex items-center justify-center"
                    x-text="openInvoices.length"
                ></span>
            </button>

            <div class="text-sm text-gray-500">
                Kasir:
                <span class="font-semibold text-gray-800">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </div>
    </div>


    {{-- ACTIVE OPEN INVOICE --}}
    <div
        x-show="activeOpenInvoiceId"
        x-cloak
        class="bg-blue-50 border border-blue-200 rounded-xl p-4"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 14.25l6-6m-5.25-3h6.5A2.75 2.75 0 0119 8v8a2.75 2.75 0 01-2.75 2.75h-8.5A2.75 2.75 0 015 16V8a2.75 2.75 0 012.75-2.75h1.5"/>
                    </svg>
                </div>

                <div>
                    <p class="text-sm font-semibold text-blue-900">
                        Open Invoice sedang dibuka
                    </p>

                    <p class="text-xs text-blue-700 mt-1">
                        <span x-text="activeOpenInvoiceNumber"></span>
                        · Stok barang langsung berkurang saat barang diambil.
                        Pembayaran bisa dicicil kapan saja.
                    </p>
                </div>
            </div>

            <button
                type="button"
                @click="exitOpenInvoice()"
                class="text-xs font-semibold text-blue-700 hover:text-blue-900"
            >
                Tutup Open Invoice
            </button>
        </div>
    </div>


    {{-- SEARCH --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="relative">
            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0010.6 10.6Z"/>
            </svg>

            <input
                type="text"
                x-model="search"
                placeholder="Cari nama produk atau SKU..."
                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent outline-none"
            >
        </div>
    </div>


    {{-- MAIN --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- PRODUCTS --}}
        <div class="xl:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl">

                <div class="p-4 border-b border-gray-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">Produk</h3>
                            <p class="text-xs text-gray-500">
                                Pilih produk yang ingin dijual
                            </p>
                        </div>

                        <span
                            class="text-xs text-gray-500 whitespace-nowrap"
                            x-text="filteredProducts.length + ' produk'"
                        ></span>
                    </div>

                    <div class="mt-4">
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">

                            <button
                                type="button"
                                @click="selectedCategory = 'all'"
                                class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium border transition"
                                :class="selectedCategory === 'all'
                                    ? 'bg-gray-900 text-white border-gray-900'
                                    : 'bg-white text-gray-700 border-gray-200 hover:border-gray-900'"
                            >
                                Semua
                            </button>

                            <template x-for="category in categories" :key="category">
                                <button
                                    type="button"
                                    @click="selectedCategory = category"
                                    class="flex-shrink-0 px-4 py-2 rounded-lg text-sm font-medium border transition"
                                    :class="selectedCategory === category
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200 hover:border-gray-900'"
                                    x-text="category"
                                ></button>
                            </template>

                        </div>
                    </div>
                </div>


                {{-- PRODUCT LIST --}}
                <div class="p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">

                        @forelse ($products as $product)

                            @php
                                $productJson = $productsJson->firstWhere('id', $product->id);
                                $productImage = $productJson['image'] ?? null;
                            @endphp

                            <button
                                type="button"
                                @click="openVariant({{ $product->id }})"
                                x-show="isProductVisible({{ $product->id }})"
                                x-cloak
                                class="w-full text-left border border-gray-200 rounded-xl overflow-hidden hover:border-gray-400 hover:shadow-sm transition bg-white"
                            >

                                <div class="aspect-square bg-gray-100 overflow-hidden">

                                    @if ($productImage)
                                        <img
                                            src="{{ $productImage }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4-4a3 3 0 014 0l2 2 1-1a3 3 0 014 0l1 1M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5v14a1 1 0 001 1z"/>
                                            </svg>
                                        </div>
                                    @endif

                                </div>

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

                        @empty

                            <div class="col-span-full py-12 text-center text-gray-500">
                                Tidak ada produk tersedia.
                            </div>

                        @endforelse

                    </div>


                    <div
                        x-show="filteredProducts.length === 0 && products.length > 0"
                        x-cloak
                        class="py-12 text-center"
                    >
                        <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="m21 21-4.35-4.35A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0010.6 10.6Z"/>
                            </svg>
                        </div>

                        <p class="mt-3 text-sm font-medium text-gray-700">
                            Produk tidak ditemukan
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Coba gunakan kata kunci atau kategori lain.
                        </p>
                    </div>
                </div>

            </div>
        </div>


        {{-- CART --}}
        <div>
            <div class="bg-white border border-gray-200 rounded-xl sticky top-20">

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
                                <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 2h13m-5 4a1 1 0 1 1-2 0m8 0a1 1 0 1 1-2 0"/>
                                </svg>
                            </div>

                            <p class="mt-3 text-sm text-gray-500">
                                Keranjang masih kosong
                            </p>
                        </div>
                    </template>


                    <div class="divide-y divide-gray-100">

                        <template x-for="(item, index) in cart" :key="item.key">

                            <div class="p-4">

                                <div class="flex gap-3">

                                    <template x-if="item.image">
                                        <img
                                            :src="item.image"
                                            class="w-14 h-14 rounded-lg object-cover bg-gray-100"
                                            alt=""
                                        >
                                    </template>

                                    <template x-if="!item.image">
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                            —
                                        </div>
                                    </template>

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
                                        x-show="!activeOpenInvoiceId || !item.persisted"
                                        x-cloak
                                        @click="removeItem(index)"
                                        class="text-gray-400 hover:text-red-500 text-xl leading-none"
                                    >
                                        ×
                                    </button>

                                </div>


                                <div class="flex items-center justify-between mt-3">

                                    <div class="flex items-center border border-gray-200 rounded-lg">

                                        <template x-if="!activeOpenInvoiceId || !item.persisted">
                                            <input
                                                type="number"
                                                min="1"
                                                x-model.number="item.quantity"
                                                @blur="item.quantity = Math.max(1, Number(item.quantity) || 1)"
                                                class="w-16 h-8 text-center text-sm font-semibold border-0 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                            >
                                        </template>

                                        <template x-if="activeOpenInvoiceId && item.persisted">
                                            <span
                                                class="w-10 text-center text-sm font-semibold"
                                                x-text="item.quantity"
                                            ></span>
                                        </template>

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


                {{-- CHECKOUT --}}
                <div class="p-4 border-t border-gray-200 space-y-4">

                    <template x-if="!activeOpenInvoiceId">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total</span>
                            <span
                                class="text-xl font-bold text-gray-900"
                                x-text="formatRupiah(total)"
                            ></span>
                        </div>
                    </template>


                    <template x-if="activeOpenInvoiceId">
                        <div class="space-y-2">

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    Total Open Invoice
                                </span>

                                <span
                                    class="font-bold text-gray-900"
                                    x-text="formatRupiah(total)"
                                ></span>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">
                                    Sudah dibayar
                                </span>

                                <span
                                    class="font-semibold text-green-600"
                                    x-text="formatRupiah(openInvoicePaid)"
                                ></span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                <span class="text-sm font-semibold text-gray-700">
                                    Sisa tagihan
                                </span>

                                <span
                                    class="text-lg font-bold text-blue-700"
                                    x-text="formatRupiah(openInvoiceRemaining)"
                                ></span>
                            </div>

                            <template x-if="pendingItems.length > 0">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-2">
                                    <p class="text-xs text-yellow-800">
                                        Ada
                                        <span
                                            class="font-semibold"
                                            x-text="pendingItems.length"
                                        ></span>
                                        item baru yang belum diambil.
                                    </p>
                                </div>
                            </template>

                        </div>
                    </template>


                    {{-- HOLD --}}
                    <button
                        type="button"
                        x-show="!activeOpenInvoiceId"
                        x-cloak
                        @click="holdInvoice()"
                        :disabled="cart.length === 0 || savingInvoice"
                        class="w-full border border-gray-300 text-gray-800 py-3 rounded-lg font-semibold hover:border-gray-900 hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed transition"
                    >
                        <span x-show="!savingInvoice">
                            Buat Open Invoice & Ambil Barang
                        </span>

                        <span x-show="savingInvoice">
                            Memproses...
                        </span>
                    </button>


                    {{-- TAKE --}}
                    <button
                        type="button"
                        x-show="activeOpenInvoiceId"
                        x-cloak
                        @click="takeItems()"
                        :disabled="pendingItems.length === 0 || takingItems"
                        class="w-full bg-gray-900 text-white py-3 rounded-lg font-semibold hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                    >
                        <span x-show="!takingItems">
                            Ambil Barang
                        </span>

                        <span x-show="takingItems">
                            Memproses pengambilan...
                        </span>
                    </button>


                    {{-- PAYMENT FORM --}}
                    <form
                        method="POST"
                        :action="activeOpenInvoiceId
                            ? '{{ url('/pos/open-invoice') }}/' + activeOpenInvoiceId + '/checkout'
                            : '{{ route('pos.store') }}'"
                        @submit="prepareSubmit($event)"
                    >

                        @csrf

                        <div id="checkout-items"></div>


                        {{-- PAYMENT METHOD --}}
                        <div class="space-y-2">

                            <label class="text-sm font-medium text-gray-700">
                                Metode Pembayaran
                            </label>

                            <div
                                class="grid gap-2"
                                :class="activeOpenInvoiceId
                                    ? 'grid-cols-2'
                                    : 'grid-cols-3'"
                            >

                                <button
                                    type="button"
                                    @click="paymentMethod = 'cash'; paid = 0"
                                    :class="paymentMethod === 'cash'
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200'"
                                    class="border rounded-lg py-2.5 text-sm font-medium transition"
                                >
                                    Cash
                                </button>

                                <button
                                    type="button"
                                    @click="paymentMethod = 'qris'; paid = activeOpenInvoiceId ? openInvoiceRemaining : total"
                                    :class="paymentMethod === 'qris'
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200'"
                                    class="border rounded-lg py-2.5 text-sm font-medium transition"
                                >
                                    QRIS
                                </button>

                                <button
                                    type="button"
                                    x-show="!activeOpenInvoiceId"
                                    x-cloak
                                    @click="paymentMethod = 'credit'; paid = 0"
                                    :class="paymentMethod === 'credit'
                                        ? 'bg-gray-900 text-white border-gray-900'
                                        : 'bg-white text-gray-700 border-gray-200'"
                                    class="border rounded-lg py-2.5 text-sm font-medium transition"
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


                        {{-- CREDIT --}}
                        <div
                            x-show="paymentMethod === 'credit' && !activeOpenInvoiceId"
                            x-cloak
                            class="space-y-3"
                        >

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">
                                    Nama Customer
                                </label>

                                <input
                                    type="text"
                                    name="customer_name"
                                    x-model="customerName"
                                    :required="paymentMethod === 'credit' && !activeOpenInvoiceId"
                                    class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                    placeholder="Masukkan nama customer"
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">
                                    No. HP Customer
                                </label>

                                <input
                                    type="text"
                                    name="customer_phone"
                                    x-model="customerPhone"
                                    :required="paymentMethod === 'credit' && !activeOpenInvoiceId"
                                    class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                    placeholder="Masukkan nomor HP customer"
                                >
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">
                                    Jatuh Tempo
                                </label>

                                <input
                                    type="date"
                                    name="due_date"
                                    x-model="dueDate"
                                    :required="paymentMethod === 'credit' && !activeOpenInvoiceId"
                                    min="{{ date('Y-m-d') }}"
                                    class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                >
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-3 py-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    Transaksi Kredit
                                </p>

                                <p class="text-xs text-yellow-700 mt-1">
                                    Barang langsung mengurangi stok dan pembayaran dapat dilakukan kemudian.
                                </p>
                            </div>

                        </div>


                        {{-- OPEN INVOICE INFO --}}
                        <div
                            x-show="activeOpenInvoiceId"
                            x-cloak
                            class="bg-blue-50 border border-blue-200 rounded-lg px-3 py-3"
                        >

                            <p class="text-sm font-medium text-blue-800">
                                Pembayaran Open Invoice
                            </p>

                            <div class="mt-2 space-y-1 text-xs text-blue-700">

                                <div class="flex justify-between">
                                    <span>Total Tagihan</span>
                                    <span x-text="formatRupiah(total)"></span>
                                </div>

                                <div class="flex justify-between">
                                    <span>Sudah Dibayar</span>
                                    <span x-text="formatRupiah(openInvoicePaid)"></span>
                                </div>

                                <div class="flex justify-between font-semibold">
                                    <span>Sisa Tagihan</span>
                                    <span x-text="formatRupiah(openInvoiceRemaining)"></span>
                                </div>

                            </div>

                            <p class="text-xs text-blue-700 mt-2">
                                Cash boleh lebih dari sisa tagihan dan kelebihannya menjadi kembalian.
                                QRIS tidak boleh melebihi sisa tagihan.
                            </p>

                        </div>


                        {{-- PAYMENT INPUT --}}
                        <div
                            class="space-y-2"
                            x-show="paymentMethod !== 'credit'"
                            x-cloak
                        >

                            <label class="text-sm font-medium text-gray-700">
                                <span
                                    x-text="activeOpenInvoiceId
                                        ? 'Nominal Pembayaran'
                                        : 'Uang Dibayar'"
                                ></span>
                            </label>

                            <input
                                type="number"
                                x-model.number="paid"
                                name="paid"
                                min="0"
                                :readonly="paymentMethod === 'qris'"
                                :value="paymentMethod === 'qris'
                                    ? (activeOpenInvoiceId
                                        ? openInvoiceRemaining
                                        : total)
                                    : paid"
                                class="w-full px-3 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-gray-900 outline-none"
                                placeholder="Masukkan nominal"
                            >

                        </div>


                        {{-- CHANGE --}}
                        <div
                            x-show="
                                paymentMethod === 'cash' &&
                                (
                                    activeOpenInvoiceId
                                        ? Number(paid) > Number(openInvoiceRemaining)
                                        : Number(paid) > Number(total)
                                )
                            "
                            x-cloak
                            class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-3"
                        >

                            <span class="text-sm text-green-700">
                                Kembalian
                            </span>

                            <span
                                class="font-bold text-green-700"
                                x-text="formatRupiah(
                                    activeOpenInvoiceId
                                        ? Math.max(
                                            0,
                                            Number(paid) - Number(openInvoiceRemaining)
                                        )
                                        : Math.max(
                                            0,
                                            Number(paid) - Number(total)
                                        )
                                )"
                            ></span>

                        </div>


                        {{-- CREDIT REMAINING --}}
                        <div
                            x-show="paymentMethod === 'credit' && !activeOpenInvoiceId && total > 0"
                            x-cloak
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


                        {{-- SUBMIT --}}
                        <button
                            type="submit"
                            :disabled="
                                activeOpenInvoiceId
                                    ? (
                                        pendingItems.length > 0 ||
                                        openInvoiceRemaining <= 0 ||
                                        Number(paid) <= 0 ||
                                        (
                                            paymentMethod === 'qris' &&
                                            Number(paid) > Number(openInvoiceRemaining)
                                        )
                                    )
                                    : (
                                        cart.length === 0 ||
                                        (
                                            paymentMethod !== 'credit' &&
                                            Number(paid) < Number(total)
                                        ) ||
                                        (
                                            paymentMethod === 'credit' &&
                                            (
                                                !customerName.trim() ||
                                                !customerPhone.trim() ||
                                                !dueDate
                                            )
                                        )
                                    )
                            "
                            class="w-full mt-3 bg-gray-900 text-white py-3 rounded-lg font-semibold hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                        >

                            <span
                                x-show="!activeOpenInvoiceId && paymentMethod !== 'credit'"
                            >
                                Bayar Sekarang
                            </span>

                            <span
                                x-show="!activeOpenInvoiceId && paymentMethod === 'credit'"
                            >
                                Simpan Transaksi Kredit
                            </span>

                            <span
                                x-show="activeOpenInvoiceId"
                                x-cloak
                            >
                                Bayar Open Invoice
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

        <div
            class="absolute inset-0 bg-black/50"
            @click="closeVariantModal()"
        ></div>

        <div
            class="relative bg-white rounded-2xl w-full max-w-lg shadow-xl max-h-[90vh] overflow-y-auto"
            @click.stop
        >

            <div class="p-5 border-b border-gray-200 sticky top-0 bg-white z-10">

                <div class="flex items-center justify-between">

                    <div class="min-w-0">
                        <h3 class="font-bold text-lg text-gray-900">
                            Pilih Varian
                        </h3>

                        <p
                            class="text-sm text-gray-500 mt-1 truncate"
                            x-text="selectedProduct ? selectedProduct.name : ''"
                        ></p>
                    </div>

                    <button
                        type="button"
                        @click="closeVariantModal()"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-700 text-2xl ml-3"
                    >
                        ×
                    </button>

                </div>

            </div>


            {{-- GALLERY --}}
            <div class="px-5 pt-5">

                <div class="relative">

                    <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">

                        <template x-if="currentImages.length > 0">

                            <img
                                :src="currentImages[currentImageIndex]"
                                class="w-full h-full object-cover"
                                alt=""
                                x-bind:key="'gallery-' + currentImageIndex + '-' + (currentImages[currentImageIndex] || '')"
                            >

                        </template>

                        <template x-if="currentImages.length === 0">

                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">

                                <svg class="w-14 h-14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4-4a3 3 0 014 0l2 2 1-1a3 3 0 014 0l1 1M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5v14a1 1 0 001 1z"/>
                                </svg>

                                <span class="text-xs mt-2">
                                    Belum ada gambar
                                </span>

                            </div>

                        </template>

                    </div>


                    <button
                        type="button"
                        x-show="currentImages.length > 1"
                        x-cloak
                        @click="previousImage()"
                        class="absolute left-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center hover:bg-white transition text-xl"
                    >
                        ‹
                    </button>

                    <button
                        type="button"
                        x-show="currentImages.length > 1"
                        x-cloak
                        @click="nextImage()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white/90 shadow flex items-center justify-center hover:bg-white transition text-xl"
                    >
                        ›
                    </button>

                </div>


                <div
                    x-show="currentImages.length > 1"
                    x-cloak
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
                            :class="currentImageIndex === index
                                ? 'border-gray-900'
                                : 'border-gray-200'"
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


            {{-- DYNAMIC VARIANT --}}
            <div class="p-5 space-y-5">

                <template
                    x-for="(group, groupIndex) in selectedProduct?.variant_groups || []"
                    :key="groupIndex"
                >

                    <div>

                        <div class="flex items-center justify-between mb-3">

                            <label class="text-sm font-semibold text-gray-900">
                                <span x-text="group.name"></span>
                            </label>

                            <span
                                x-show="selectedAttributes[groupIndex]"
                                x-cloak
                                class="text-xs text-gray-500"
                                x-text="selectedAttributes[groupIndex]"
                            ></span>

                        </div>


                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

                            <template
                                x-for="value in group.values"
                                :key="getValueName(value)"
                            >

                                <button
                                    type="button"
                                    @click="selectAttribute(groupIndex, getValueName(value))"
                                    :disabled="!isAttributeAvailable(groupIndex, getValueName(value))"
                                    class="relative rounded-xl border overflow-hidden transition text-left"
                                    :class="
                                        selectedAttributes[groupIndex] === getValueName(value)
                                            ? 'border-gray-900 ring-2 ring-gray-900'
                                            : isAttributeAvailable(groupIndex, getValueName(value))
                                                ? 'border-gray-200 hover:border-gray-900'
                                                : 'border-gray-200 bg-gray-50 opacity-50 cursor-not-allowed'
                                    "
                                >

                                    <template x-if="getValueImage(value)">

                                        <div class="aspect-[4/3] bg-gray-100 overflow-hidden">

                                            <img
                                                :src="normalizeImage(getValueImage(value))"
                                                :alt="getValueName(value)"
                                                class="w-full h-full object-cover"
                                            >

                                        </div>

                                    </template>


                                    <template x-if="!getValueImage(value)">

                                        <div class="h-16 bg-gray-50 flex items-center justify-center text-gray-400">

                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4-4a3 3 0 014 0l2 2 1-1a3 3 0 014 0l1 1M5 20h14a1 1 0 001-1V5a1 1 0 00-1-1H5v14a1 1 0 001 1z"/>
                                            </svg>

                                        </div>

                                    </template>


                                    <div class="px-3 py-2.5">

                                        <div class="flex items-center justify-between gap-2">

                                            <span
                                                class="text-sm font-medium"
                                                :class="
                                                    selectedAttributes[groupIndex] === getValueName(value)
                                                        ? 'text-gray-900'
                                                        : 'text-gray-700'
                                                "
                                                x-text="getValueName(value)"
                                            ></span>

                                            <template
                                                x-if="selectedAttributes[groupIndex] === getValueName(value)"
                                            >

                                                <span class="w-5 h-5 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs">
                                                    ✓
                                                </span>

                                            </template>

                                        </div>

                                    </div>

                                </button>

                            </template>

                        </div>

                    </div>

                </template>


                {{-- SELECTED VARIANT --}}
                <template x-if="selectedVariant">

                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">

                        <div class="flex items-start justify-between gap-4">

                            <div class="min-w-0">

                                <p class="text-xs text-gray-500">
                                    Varian dipilih
                                </p>

                                <p
                                    class="font-semibold text-gray-900 mt-1 break-words"
                                    x-text="selectedVariant.label"
                                ></p>

                            </div>

                            <div class="text-right flex-shrink-0">

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

                            <div class="flex items-center justify-between gap-4">

                                <div>

                                    <p class="text-xs text-gray-500">
                                        Harga
                                    </p>

                                    <p
                                        class="font-bold text-gray-900 mt-1"
                                        x-text="formatRupiah(getVariantPrice(selectedVariant))"
                                    ></p>

                                </div>

                                <div class="text-right min-w-0">

                                    <p class="text-xs text-gray-500">
                                        SKU Variant
                                    </p>

                                    <p
                                        class="text-sm font-medium text-gray-800 mt-1 truncate max-w-[180px]"
                                        x-text="selectedVariant.sku_variant || '-'"
                                    ></p>

                                </div>

                            </div>

                        </div>

                    </div>

                </template>


                {{-- INFO --}}
                <template
                    x-if="
                        selectedProduct &&
                        selectedProduct.variant_groups.length > 0 &&
                        !selectedVariant
                    "
                >

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">

                        <div class="flex gap-3">

                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                            </svg>

                            <div>

                                <p class="text-sm font-medium text-blue-800">
                                    Pilih varian terlebih dahulu
                                </p>

                                <p class="text-xs text-blue-700 mt-1">
                                    Pilih semua varian yang tersedia untuk melanjutkan.
                                </p>

                            </div>

                        </div>

                    </div>

                </template>


                {{-- ADD --}}
                <button
                    type="button"
                    @click="addSelectedVariant()"
                    :disabled="!selectedVariant || Number(selectedVariant.stock) <= 0"
                    class="w-full bg-gray-900 text-white py-3 rounded-lg font-semibold hover:bg-gray-800 disabled:bg-gray-300 disabled:cursor-not-allowed transition"
                >

                    <span x-show="!selectedVariant">
                        Pilih Varian
                    </span>

                    <span x-show="selectedVariant">
                        Tambah ke Keranjang
                    </span>

                </button>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- OPEN INVOICE MODAL --}}
    {{-- ========================================================= --}}

    <div
        x-show="openInvoiceModal"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4"
    >

        <div
            class="absolute inset-0 bg-black/50"
            @click="closeOpenInvoiceModal()"
        ></div>

        <div
            class="relative bg-white rounded-2xl w-full max-w-2xl shadow-xl max-h-[90vh] overflow-hidden"
            @click.stop
        >

            <div class="p-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>

                        <h3 class="font-bold text-lg text-gray-900">
                            Open Invoice
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Transaksi yang sedang ditahan
                        </p>

                    </div>

                    <button
                        type="button"
                        @click="closeOpenInvoiceModal()"
                        class="text-gray-400 hover:text-gray-700 text-2xl"
                    >
                        ×
                    </button>

                </div>

            </div>


            <div class="max-h-[65vh] overflow-y-auto">

                {{-- LOADING --}}
                <template x-if="loadingOpenInvoices">

                    <div class="py-16 text-center">

                        <div class="w-8 h-8 border-2 border-gray-300 border-t-gray-900 rounded-full animate-spin mx-auto"></div>

                        <p class="text-sm text-gray-500 mt-3">
                            Memuat Open Invoice...
                        </p>

                    </div>

                </template>


                {{-- EMPTY --}}
                <template
                    x-if="
                        !loadingOpenInvoices &&
                        openInvoices.length === 0
                    "
                >

                    <div class="py-16 text-center px-5">

                        <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 flex items-center justify-center">

                            <svg class="w-7 h-7 text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                    d="M9 14.25l6-6m-5.25-3h6.5A2.75 2.75 0 0119 8v8a2.75 2.75 0 01-2.75 2.75h-8.5A2.75 2.75 0 015 16V8a2.75 2.75 0 012.75-2.75h1.5"/>
                            </svg>

                        </div>

                        <p class="mt-3 text-sm font-medium text-gray-700">
                            Belum ada Open Invoice
                        </p>

                        <p class="text-xs text-gray-500 mt-1">
                            Simpan transaksi yang belum selesai untuk membukanya kembali nanti.
                        </p>

                    </div>

                </template>


                {{-- LIST --}}
                <div
                    x-show="!loadingOpenInvoices && openInvoices.length > 0"
                    x-cloak
                    class="divide-y divide-gray-100"
                >

                    <template
                        x-for="invoice in openInvoices"
                        :key="invoice.id"
                    >

                        <div class="p-4 hover:bg-gray-50 transition">

                            <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                                <div class="flex-1 min-w-0">

                                    <div class="flex items-center gap-2">

                                        <span
                                            class="font-semibold text-gray-900"
                                            x-text="invoice.invoice_number"
                                        ></span>

                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-medium">
                                            Open
                                        </span>

                                    </div>

                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">

                                        <span x-text="invoice.item_count + ' item'"></span>

                                        <span>•</span>

                                        <span x-text="invoice.created_at"></span>

                                    </div>

                                    <p
                                        class="font-bold text-gray-900 mt-2"
                                        x-text="formatRupiah(invoice.total)"
                                    ></p>

                                </div>


                                <div class="flex items-center gap-2">

                                    <button
                                        type="button"
                                        @click="resumeOpenInvoice(invoice.id)"
                                        :disabled="loadingInvoiceId === invoice.id"
                                        class="px-4 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 disabled:bg-gray-300 transition"
                                    >

                                        <span x-show="loadingInvoiceId !== invoice.id">
                                            Buka
                                        </span>

                                        <span x-show="loadingInvoiceId === invoice.id">
                                            Memuat...
                                        </span>

                                    </button>

                                    <button
                                        type="button"
                                        @click="cancelOpenInvoice(invoice.id)"
                                        :disabled="loadingInvoiceId === invoice.id"
                                        class="px-4 py-2.5 rounded-lg border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 disabled:opacity-50 transition"
                                    >
                                        Hapus
                                    </button>

                                </div>

                            </div>

                        </div>

                    </template>

                </div>

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

        search: '',
        selectedCategory: 'all',
        cart: [],

        /*
        |--------------------------------------------------------------------------
        | VARIANT
        |--------------------------------------------------------------------------
        */

        variantModal: false,
        selectedProduct: null,
        selectedVariant: null,
        selectedAttributes: [],
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
        | OPEN INVOICE
        |--------------------------------------------------------------------------
        */

        openInvoiceModal: false,
        openInvoices: [],
        savingInvoice: false,
        loadingOpenInvoices: false,
        loadingInvoiceId: null,

        activeOpenInvoiceId: null,

        initialOpenInvoiceId: @json($openInvoiceId ?? null),

        activeOpenInvoiceNumber: '',

        openInvoiceTotal: 0,
        openInvoicePaid: 0,

        takingItems: false,

        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        */

        products: @js($productsJson),

        /*
        |--------------------------------------------------------------------------
        | INIT
        |--------------------------------------------------------------------------
        */

        async init() {

            if (this.initialOpenInvoiceId) {

                await this.resumeOpenInvoice(
                    this.initialOpenInvoiceId
                );

            }

        },

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        get pendingItems() {

            return this.cart.filter(
                item => !item.persisted
            );

        },

        get pendingTotal() {

            return this.pendingItems.reduce(
                (sum, item) =>
                    sum +
                    (
                        Number(item.price) *
                        Number(item.quantity)
                    ),
                0
            );

        },

        get total() {

            if (this.activeOpenInvoiceId) {

                return (
                    Number(this.openInvoiceTotal) +
                    Number(this.pendingTotal)
                );

            }

            return this.cart.reduce(
                (sum, item) =>
                    sum +
                    (
                        Number(item.price) *
                        Number(item.quantity)
                    ),
                0
            );

        },

        get openInvoiceRemaining() {

            return Math.max(
                0,
                Number(this.total) -
                Number(this.openInvoicePaid)
            );

        },

        /*
        |--------------------------------------------------------------------------
        | CATEGORIES
        |--------------------------------------------------------------------------
        */

        get categories() {

            return [
                ...new Set(
                    this.products
                        .map(product => product.category)
                        .filter(category => category)
                )
            ];

        },

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        get filteredProducts() {

            const keyword =
                this.search
                    .toLowerCase()
                    .trim();

            return this.products.filter(product => {

                const matchCategory =
                    this.selectedCategory === 'all' ||
                    product.category === this.selectedCategory;

                const matchSearch =
                    !keyword ||
                    String(product.name)
                        .toLowerCase()
                        .includes(keyword) ||
                    String(product.sku)
                        .toLowerCase()
                        .includes(keyword);

                return matchCategory && matchSearch;

            });

        },

        isProductVisible(productId) {

            return this.filteredProducts.some(
                product =>
                    Number(product.id) ===
                    Number(productId)
            );

        },

        /*
        |--------------------------------------------------------------------------
        | VALUE
        |--------------------------------------------------------------------------
        */

        getValueName(value) {

            if (
                value &&
                typeof value === 'object'
            ) {

                return String(
                    value.name || ''
                ).trim();

            }

            return String(
                value || ''
            ).trim();

        },

        getValueImage(value) {

            if (!value) {
                return null;
            }

            let image = null;

            if (typeof value === 'object') {
                image = value.image || null;
            }

            if (!image) {
                return null;
            }

            return this.normalizeImage(image);

        },

        normalizeImage(image) {

            if (!image) {
                return null;
            }

            image = String(image).trim();

            if (
                image.startsWith('http://') ||
                image.startsWith('https://')
            ) {
                return image;
            }

            if (image.startsWith('/storage/')) {
                return image;
            }

            if (image.startsWith('storage/')) {
                return '/' + image;
            }

            return '/storage/' +
                image.replace(/^\/+/, '');

        },

        /*
        |--------------------------------------------------------------------------
        | FIND IMAGE
        |--------------------------------------------------------------------------
        */

        findValueImage(groupIndex, selectedValue) {

            if (!this.selectedProduct) {
                return null;
            }

            const target =
                String(selectedValue ?? '')
                    .trim()
                    .toLowerCase();

            if (!target) {
                return null;
            }

            const groups =
                Array.isArray(
                    this.selectedProduct.variant_groups
                )
                    ? this.selectedProduct.variant_groups
                    : [];

            const group =
                groups[groupIndex];

            if (
                group &&
                Array.isArray(group.values)
            ) {

                const found =
                    group.values.find(value => {

                        const name =
                            this.getValueName(value)
                                .trim()
                                .toLowerCase();

                        return name === target;

                    });

                if (found) {

                    const image =
                        this.getValueImage(found);

                    if (image) {
                        return image;
                    }

                }

            }

            const imagesByValue =
                this.selectedProduct.images_by_value || {};

            const groupImages =
                imagesByValue[groupIndex] ??
                imagesByValue[String(groupIndex)] ??
                {};

            if (
                groupImages &&
                typeof groupImages === 'object'
            ) {

                for (
                    const [name, images]
                    of Object.entries(groupImages)
                ) {

                    if (
                        String(name)
                            .trim()
                            .toLowerCase() !== target
                    ) {
                        continue;
                    }

                    if (
                        Array.isArray(images) &&
                        images.length
                    ) {

                        return this.normalizeImage(
                            images[0]
                        );

                    }

                    if (images) {

                        return this.normalizeImage(
                            images
                        );

                    }

                }

            }

            return null;

        },

        findImagesByValue(groupIndex, selectedValue) {

            if (!this.selectedProduct) {
                return [];
            }

            const key =
                String(selectedValue ?? '')
                    .trim()
                    .toLowerCase();

            if (!key) {
                return [];
            }

            const imagesByValue =
                this.selectedProduct.images_by_value || {};

            const groupImages =
                imagesByValue[String(groupIndex)]
                ??
                imagesByValue[groupIndex]
                ??
                {};

            if (
                !groupImages ||
                typeof groupImages !== 'object'
            ) {
                return [];
            }

            let images = [];

            if (
                Array.isArray(
                    groupImages[key]
                )
            ) {

                images.push(
                    ...groupImages[key]
                );

            }

            for (
                const [storedKey, storedImages]
                of Object.entries(groupImages)
            ) {

                if (
                    String(storedKey)
                        .trim()
                        .toLowerCase() !== key
                ) {
                    continue;
                }

                if (
                    Array.isArray(storedImages)
                ) {

                    images.push(
                        ...storedImages
                    );

                }

            }

            return [
                ...new Set(
                    images
                        .map(
                            image =>
                                this.normalizeImage(image)
                        )
                        .filter(image => image)
                )
            ];

        },

        /*
        |--------------------------------------------------------------------------
        | OPEN VARIANT
        |--------------------------------------------------------------------------
        */

        openVariant(productId) {

            const product =
                this.products.find(
                    product =>
                        Number(product.id) ===
                        Number(productId)
                );

            if (!product) {
                return;
            }

            this.selectedProduct =
                product;

            this.selectedVariant =
                null;

            this.selectedAttributes =
                [];

            this.currentImageIndex =
                0;

            this.currentImages =
                product.image
                    ? [
                        this.normalizeImage(
                            product.image
                        )
                    ]
                    : [];

            if (
                !Array.isArray(
                    product.variant_groups
                ) ||
                product.variant_groups.length === 0
            ) {

                const availableVariant =
                    (
                        product.variants ||
                        []
                    ).find(
                        variant =>
                            Number(
                                variant.stock
                            ) > 0
                    );

                if (availableVariant) {

                    this.selectedVariant =
                        availableVariant;

                    this.selectedAttributes =
                        Array.isArray(
                            availableVariant.attributes
                        )
                            ? [
                                ...availableVariant.attributes
                            ]
                            : [];

                    this.updateGallery();

                }

            }

            if (
                Array.isArray(
                    product.variant_groups
                ) &&
                product.variant_groups.length > 0 &&
                Array.isArray(
                    product.variants
                ) &&
                product.variants.length === 1
            ) {

                const variant =
                    product.variants[0];

                if (
                    Number(variant.stock) > 0
                ) {

                    this.selectedAttributes =
                        Array.isArray(
                            variant.attributes
                        )
                            ? [
                                ...variant.attributes
                            ]
                            : [];

                    this.selectedVariant =
                        variant;

                    this.updateGallery();

                }

            }

            this.variantModal =
                true;

        },

        /*
        |--------------------------------------------------------------------------
        | SELECT ATTRIBUTE
        |--------------------------------------------------------------------------
        */

        selectAttribute(groupIndex, value) {

            if (!this.selectedProduct) {
                return;
            }

            const cleanValue =
                this.getValueName(value);

            if (!cleanValue) {
                return;
            }

            this.selectedAttributes[groupIndex] =
                cleanValue;

            const groupCount =
                Array.isArray(
                    this.selectedProduct.variant_groups
                )
                    ? this.selectedProduct.variant_groups.length
                    : 0;

            for (
                let i = groupIndex + 1;
                i < groupCount;
                i++
            ) {

                this.selectedAttributes[i] =
                    null;

            }

            const selectedImage =
                this.findValueImage(
                    groupIndex,
                    cleanValue
                );

            if (selectedImage) {

                this.currentImages = [
                    selectedImage
                ];

                this.currentImageIndex = 0;

            }

            this.selectedVariant =
                this.findSelectedVariant();

            if (!selectedImage) {

                this.updateGallery();

            }

        },

        /*
        |--------------------------------------------------------------------------
        | AVAILABLE
        |--------------------------------------------------------------------------
        */

        isAttributeAvailable(
            groupIndex,
            value
        ) {

            if (!this.selectedProduct) {
                return false;
            }

            const variants =
                this.selectedProduct.variants || [];

            const normalizedValue =
                String(value)
                    .toLowerCase()
                    .trim();

            return variants.some(
                variant => {

                    if (
                        Number(
                            variant.stock
                        ) <= 0
                    ) {
                        return false;
                    }

                    const attributes =
                        Array.isArray(
                            variant.attributes
                        )
                            ? variant.attributes
                            : [];

                    if (
                        String(
                            attributes[groupIndex]
                            ?? ''
                        )
                            .toLowerCase()
                            .trim() !==
                        normalizedValue
                    ) {
                        return false;
                    }

                    for (
                        let i = 0;
                        i < groupIndex;
                        i++
                    ) {

                        const selected =
                            this.selectedAttributes[i];

                        if (!selected) {
                            continue;
                        }

                        if (
                            String(
                                attributes[i] ?? ''
                            )
                                .toLowerCase()
                                .trim() !==
                            String(selected)
                                .toLowerCase()
                                .trim()
                        ) {
                            return false;
                        }

                    }

                    return true;

                }
            );

        },

        /*
        |--------------------------------------------------------------------------
        | FIND SELECTED
        |--------------------------------------------------------------------------
        */

        findSelectedVariant() {

            if (!this.selectedProduct) {
                return null;
            }

            const groups =
                this.selectedProduct
                    .variant_groups || [];

            if (groups.length === 0) {

                return (
                    (
                        this.selectedProduct
                            .variants ||
                        []
                    ).find(
                        variant =>
                            Number(
                                variant.stock
                            ) > 0
                    )
                    || null
                );

            }

            for (
                let i = 0;
                i < groups.length;
                i++
            ) {

                if (
                    !this.selectedAttributes[i]
                ) {

                    return null;

                }

            }

            return (
                (
                    this.selectedProduct
                        .variants ||
                    []
                ).find(
                    variant => {

                        if (
                            Number(
                                variant.stock
                            ) <= 0
                        ) {

                            return false;

                        }

                        const attributes =
                            Array.isArray(
                                variant.attributes
                            )
                                ? variant.attributes
                                : [];

                        if (
                            attributes.length !==
                            groups.length
                        ) {

                            return false;

                        }

                        for (
                            let i = 0;
                            i < groups.length;
                            i++
                        ) {

                            if (
                                String(
                                    attributes[i] ?? ''
                                )
                                    .toLowerCase()
                                    .trim() !==
                                String(
                                    this.selectedAttributes[i] ?? ''
                                )
                                    .toLowerCase()
                                    .trim()
                            ) {

                                return false;

                            }

                        }

                        return true;

                    }
                )
                || null
            );

        },

        /*
        |--------------------------------------------------------------------------
        | GALLERY
        |--------------------------------------------------------------------------
        */

        updateGallery() {

            if (!this.selectedProduct) {

                this.currentImages = [];
                this.currentImageIndex = 0;

                return;

            }

            let images = [];

            if (
                Array.isArray(
                    this.selectedAttributes
                )
            ) {

                this.selectedAttributes.forEach(
                    (selectedValue, groupIndex) => {

                        if (!selectedValue) {
                            return;
                        }

                        const image =
                            this.findValueImage(
                                groupIndex,
                                selectedValue
                            );

                        if (image) {

                            images.push(image);

                        }

                    }
                );

            }

            if (
                this.selectedVariant &&
                Array.isArray(
                    this.selectedVariant.images
                )
            ) {

                this.selectedVariant.images.forEach(
                    image => {

                        const normalized =
                            this.normalizeImage(image);

                        if (normalized) {
                            images.push(normalized);
                        }

                    }
                );

            }

            if (
                images.length === 0 &&
                this.selectedProduct.image
            ) {

                const productImage =
                    this.normalizeImage(
                        this.selectedProduct.image
                    );

                if (productImage) {
                    images.push(productImage);
                }

            }

            images = [
                ...new Set(
                    images.filter(
                        image => image
                    )
                )
            ];

            this.currentImages =
                images;

            this.currentImageIndex =
                0;

        },

        /*
        |--------------------------------------------------------------------------
        | PRICE
        |--------------------------------------------------------------------------
        */

        getVariantPrice(variant) {

            if (!variant) {

                return Number(
                    this.selectedProduct?.price ||
                    0
                );

            }

            if (
                variant.price !== null &&
                variant.price !== undefined &&
                Number(variant.price) > 0
            ) {

                return Number(
                    variant.price
                );

            }

            return Number(
                this.selectedProduct?.price ||
                0
            );

        },

        /*
        |--------------------------------------------------------------------------
        | ADD VARIANT
        |--------------------------------------------------------------------------
        */

        addSelectedVariant() {

            if (!this.selectedProduct) {
                return;
            }

            if (!this.selectedVariant) {

                alert(
                    'Silakan pilih semua varian terlebih dahulu.'
                );

                return;

            }

            if (
                Number(
                    this.selectedVariant.stock
                ) <= 0
            ) {

                alert(
                    'Stok varian habis.'
                );

                return;

            }

            this.addToCart(
                this.selectedVariant
            );

        },

        /*
        |--------------------------------------------------------------------------
        | ADD CART
        |--------------------------------------------------------------------------
        */

        addToCart(variant) {

            if (!this.selectedProduct || !variant) {
                return;
            }

            const stock =
                Number(variant.stock);

            if (stock <= 0) {
                alert('Stok varian habis.');
                return;
            }

            const baseKey =
                String(this.selectedProduct.id) +
                '-' +
                String(variant.id);

            let key =
                baseKey;

            if (this.activeOpenInvoiceId) {

                const pending =
                    this.cart.find(
                        item =>
                            !item.persisted &&
                            item.product_id ==
                                this.selectedProduct.id &&
                            item.product_variant_id ==
                                variant.id
                    );

                if (pending) {

                    pending.quantity++;

                    this.closeVariantModal();

                    return;

                }

                key =
                    baseKey +
                    '-new-' +
                    Date.now() +
                    '-' +
                    Math.random()
                        .toString(36)
                        .slice(2, 8);

            } else {

                const existing =
                    this.cart.find(
                        item =>
                            item.key === baseKey
                    );

                if (existing) {

                    if (
                        existing.quantity < stock
                    ) {

                        existing.quantity++;

                    } else {

                        alert(
                            'Jumlah melebihi stok yang tersedia.'
                        );

                    }

                    this.closeVariantModal();

                    return;

                }

            }

            let image = null;

            if (
                Array.isArray(
                    variant.attributes
                )
            ) {

                for (
                    let i = 0;
                    i < variant.attributes.length;
                    i++
                ) {

                    const value =
                        variant.attributes[i];

                    if (!value) {
                        continue;
                    }

                    const directImage =
                        this.findValueImage(
                            i,
                            value
                        );

                    if (directImage) {

                        image =
                            directImage;

                        break;

                    }

                    const byValueImages =
                        this.findImagesByValue(
                            i,
                            value
                        );

                    if (
                        byValueImages.length
                    ) {

                        image =
                            byValueImages[0];

                        break;

                    }

                }

            }

            if (
                !image &&
                Array.isArray(
                    variant.images
                ) &&
                variant.images.length
            ) {

                image =
                    this.normalizeImage(
                        variant.images[0]
                    );

            }

            if (
                !image &&
                this.selectedProduct.image
            ) {

                image =
                    this.normalizeImage(
                        this.selectedProduct.image
                    );

            }

            this.cart.push({

                key,

                product_id:
                    this.selectedProduct.id,

                product_variant_id:
                    variant.id,

                name:
                    this.selectedProduct.name,

                variant:
                    variant.label ||
                    (
                        Array.isArray(
                            variant.attributes
                        )
                            ? variant.attributes.join(' / ')
                            : '-'
                    ),

                attributes:
                    Array.isArray(
                        variant.attributes
                    )
                        ? [
                            ...variant.attributes
                        ]
                        : [],

                sku_variant:
                    variant.sku_variant,

                price:
                    this.getVariantPrice(
                        variant
                    ),

                image,

                stock,

                quantity: 1,

                persisted: false

            });

            this.closeVariantModal();

        },

        /*
        |--------------------------------------------------------------------------
        | CLOSE
        |--------------------------------------------------------------------------
        */

        closeVariantModal() {

            this.variantModal =
                false;

            this.selectedProduct =
                null;

            this.selectedVariant =
                null;

            this.selectedAttributes =
                [];

            this.currentImages =
                [];

            this.currentImageIndex =
                0;

        },

        /*
        |--------------------------------------------------------------------------
        | IMAGE
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
                    this.currentImageIndex +
                    1
                ) %
                this.currentImages.length;

        },

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
        | QUANTITY
        |--------------------------------------------------------------------------
        */

        increase(index) {

            const item =
                this.cart[index];

            if (
                !item ||
                (
                    this.activeOpenInvoiceId &&
                    item.persisted
                )
            ) {
                return;
            }

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

        decrease(index) {

            const item =
                this.cart[index];

            if (
                !item ||
                (
                    this.activeOpenInvoiceId &&
                    item.persisted
                )
            ) {
                return;
            }

            if (
                item.quantity > 1
            ) {

                item.quantity--;

            } else {

                this.removeItem(index);

            }

        },

        removeItem(index) {

            const item =
                this.cart[index];

            if (
                !item ||
                (
                    this.activeOpenInvoiceId &&
                    item.persisted
                )
            ) {
                return;
            }

            this.cart.splice(
                index,
                1
            );

        },

        clearCart() {

            if (this.activeOpenInvoiceId) {

                this.cart =
                    this.cart.filter(
                        item => item.persisted
                    );

                this.paid = 0;

                this.paymentMethod =
                    'cash';

                return;

            }

            this.cart = [];

            this.paid = 0;

            this.customerName = '';
            this.customerPhone = '';
            this.dueDate = '';

            this.paymentMethod =
                'cash';

        },

        /*
        |--------------------------------------------------------------------------
        | FORMAT
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
                Number(value) || 0
            );

        },

        /*
        |--------------------------------------------------------------------------
        | OPEN INVOICE MODAL
        |--------------------------------------------------------------------------
        */

        async openOpenInvoiceModal() {

            this.openInvoiceModal =
                true;

            await this.loadOpenInvoices();

        },

        closeOpenInvoiceModal() {

            this.openInvoiceModal =
                false;

        },

        /*
        |--------------------------------------------------------------------------
        | LOAD OPEN INVOICES
        |--------------------------------------------------------------------------
        */

        async loadOpenInvoices() {

            this.loadingOpenInvoices =
                true;

            try {

                const response =
                    await fetch(
                        '{{ route('pos.open-invoice.data') }}',
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );

                if (!response.ok) {

                    throw new Error(
                        'Gagal mengambil Open Invoice.'
                    );

                }

                const data =
                    await response.json();

                this.openInvoices =
                    data.data || [];

            } catch (error) {

                console.error(error);

                alert(
                    'Gagal mengambil daftar Open Invoice.'
                );

            } finally {

                this.loadingOpenInvoices =
                    false;

            }

        },

        /*
        |--------------------------------------------------------------------------
        | HOLD
        |--------------------------------------------------------------------------
        */

        async holdInvoice() {

            if (
                this.cart.length === 0
            ) {

                alert(
                    'Keranjang masih kosong.'
                );

                return;

            }

            if (
                this.savingInvoice
            ) {
                return;
            }

            this.savingInvoice =
                true;

            try {

                const response =
                    await fetch(
                        '{{ route('pos.open-invoice.hold') }}',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        ?.getAttribute(
                                            'content'
                                        ) ||
                                    '{{ csrf_token() }}'
                            },

                            body:
                                JSON.stringify({
                                    items:
                                        this.cart.map(
                                            item => ({
                                                product_id:
                                                    item.product_id,

                                                product_variant_id:
                                                    item.product_variant_id,

                                                quantity:
                                                    item.quantity
                                            })
                                        )
                                })
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        (
                            data.errors
                                ? Object.values(
                                    data.errors
                                )
                                    .flat()
                                    .join('\n')
                                : 'Gagal membuat Open Invoice.'
                        )
                    );

                }

                const invoice =
                    data.data;

                if (!invoice?.id) {

                    throw new Error(
                        'Server belum mengembalikan data Open Invoice.'
                    );

                }

                this.activeOpenInvoiceId =
                    invoice.id;

                this.activeOpenInvoiceNumber =
                    invoice.invoice_number || '';

                this.openInvoiceTotal =
                    Number(
                        invoice.total ||
                        this.total
                    );

                this.openInvoicePaid =
                    Number(
                        invoice.paid ||
                        0
                    );

                this.cart =
                    this.cart.map(
                        item => ({
                            ...item,
                            persisted: true
                        })
                    );

                this.paid = 0;

                this.paymentMethod =
                    'cash';

                await this.loadOpenInvoices();

                alert(
                    data.message ||
                    'Open Invoice berhasil dibuat dan barang berhasil diambil.'
                );

            } catch (error) {

                console.error(error);

                alert(
                    error.message ||
                    'Gagal membuat Open Invoice.'
                );

            } finally {

                this.savingInvoice =
                    false;

            }

        },

        /*
        |--------------------------------------------------------------------------
        | TAKE ITEMS
        |--------------------------------------------------------------------------
        */

        async takeItems() {

            if (!this.activeOpenInvoiceId) {
                return;
            }

            if (
                this.pendingItems.length === 0
            ) {

                alert(
                    'Belum ada barang baru yang akan diambil.'
                );

                return;

            }

            if (this.takingItems) {
                return;
            }

            this.takingItems =
                true;

            try {

                const response =
                    await fetch(
                        '{{ url('/pos/open-invoice') }}/' +
                        this.activeOpenInvoiceId +
                        '/take',
                        {
                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        ?.getAttribute(
                                            'content'
                                        ) ||
                                    '{{ csrf_token() }}'
                            },

                            body:
                                JSON.stringify({
                                    items:
                                        this.pendingItems.map(
                                            item => ({
                                                product_id:
                                                    item.product_id,

                                                product_variant_id:
                                                    item.product_variant_id,

                                                quantity:
                                                    item.quantity
                                            })
                                        )
                                })
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        (
                            data.errors
                                ? Object.values(
                                    data.errors
                                )
                                    .flat()
                                    .join('\n')
                                : 'Gagal mengambil barang.'
                        )
                    );

                }

                const id =
                    this.activeOpenInvoiceId;

                await this.resumeOpenInvoice(id);

                alert(
                    data.message ||
                    'Barang berhasil diambil dan stok berhasil dikurangi.'
                );

            } catch (error) {

                console.error(error);

                alert(
                    error.message ||
                    'Gagal mengambil barang.'
                );

            } finally {

                this.takingItems =
                    false;

            }

        },

        /*
        |--------------------------------------------------------------------------
        | RESUME
        |--------------------------------------------------------------------------
        */

        async resumeOpenInvoice(transactionId) {

            if (
                this.loadingInvoiceId
            ) {
                return;
            }

            if (
                this.cart.length > 0 &&
                !this.activeOpenInvoiceId
            ) {

                const confirmed =
                    confirm(
                        'Keranjang saat ini masih berisi produk. Buka Open Invoice akan mengganti isi keranjang saat ini. Lanjutkan?'
                    );

                if (!confirmed) {
                    return;
                }

            }

            this.loadingInvoiceId =
                transactionId;

            try {

                const response =
                    await fetch(
                        '{{ url('/pos/open-invoice') }}/' +
                        transactionId,
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Gagal membuka Open Invoice.'
                    );

                }

                const invoice =
                    data.data;

                this.cart =
                    (invoice.items || [])
                        .map(
                            item => ({

                                key:
                                    String(
                                        item.product_id
                                    ) +
                                    '-' +
                                    String(
                                        item.product_variant_id
                                    ),

                                product_id:
                                    item.product_id,

                                product_variant_id:
                                    item.product_variant_id,

                                name:
                                    item.name,

                                variant:
                                    item.variant || '-',

                                attributes:
                                    Array.isArray(
                                        item.attributes
                                    )
                                        ? [
                                            ...item.attributes
                                        ]
                                        : [],

                                sku_variant:
                                    item.sku_variant,

                                price:
                                    Number(
                                        item.price
                                    ),

                                image:
                                    item.image
                                        ? this.normalizeImage(
                                            item.image
                                        )
                                        : null,

                                stock:
                                    Number(
                                        item.stock || 0
                                    ),

                                quantity:
                                    Number(
                                        item.quantity
                                    ),

                                persisted:
                                    true

                            })
                        );

                this.activeOpenInvoiceId =
                    invoice.id;

                this.activeOpenInvoiceNumber =
                    invoice.invoice_number;

                this.openInvoiceTotal =
                    Number(
                        invoice.total ||
                        0
                    );

                this.openInvoicePaid =
                    Number(
                        invoice.paid ||
                        0
                    );

                this.paymentMethod =
                    'cash';

                this.paid = 0;

                this.customerName = '';
                this.customerPhone = '';
                this.dueDate = '';

                this.closeOpenInvoiceModal();

            } catch (error) {

                console.error(error);

                alert(
                    error.message ||
                    'Gagal membuka Open Invoice.'
                );

            } finally {

                this.loadingInvoiceId =
                    null;

            }

        },

        /*
        |--------------------------------------------------------------------------
        | CANCEL
        |--------------------------------------------------------------------------
        */

        async cancelOpenInvoice(transactionId) {

            const invoice =
                this.openInvoices.find(
                    item =>
                        Number(item.id) ===
                        Number(transactionId)
                );

            const confirmed =
                confirm(
                    'Hapus Open Invoice ' +
                    (
                        invoice?.invoice_number ||
                        ''
                    ) +
                    '?\n\nData transaksi akan dibatalkan.'
                );

            if (!confirmed) {
                return;
            }

            this.loadingInvoiceId =
                transactionId;

            try {

                const response =
                    await fetch(
                        '{{ url('/pos/open-invoice') }}/' +
                        transactionId,
                        {
                            method: 'DELETE',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        ?.getAttribute(
                                            'content'
                                        ) ||
                                    '{{ csrf_token() }}'
                            }
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Gagal menghapus Open Invoice.'
                    );

                }

                if (
                    Number(
                        this.activeOpenInvoiceId
                    ) ===
                    Number(transactionId)
                ) {

                    this.cart = [];

                    this.activeOpenInvoiceId =
                        null;

                    this.activeOpenInvoiceNumber =
                        '';

                    this.openInvoiceTotal =
                        0;

                    this.openInvoicePaid =
                        0;

                    this.paymentMethod =
                        'cash';

                    this.paid =
                        0;

                }

                await this.loadOpenInvoices();

                alert(
                    data.message ||
                    'Open Invoice berhasil dihapus.'
                );

            } catch (error) {

                console.error(error);

                alert(
                    error.message ||
                    'Gagal menghapus Open Invoice.'
                );

            } finally {

                this.loadingInvoiceId =
                    null;

            }

        },

        /*
        |--------------------------------------------------------------------------
        | EXIT
        |--------------------------------------------------------------------------
        */

        exitOpenInvoice() {

            if (
                this.cart.length > 0
            ) {

                const confirmed =
                    confirm(
                        'Keluar dari Open Invoice?\n\nPerubahan pada keranjang belum disimpan ke Open Invoice.'
                    );

                if (!confirmed) {
                    return;
                }

            }

            this.cart = [];

            this.paid = 0;

            this.paymentMethod =
                'cash';

            this.customerName = '';
            this.customerPhone = '';
            this.dueDate = '';

            this.activeOpenInvoiceId =
                null;

            this.activeOpenInvoiceNumber =
                '';

            this.openInvoiceTotal =
                0;

            this.openInvoicePaid =
                0;

        },

        /*
        |--------------------------------------------------------------------------
        | PREPARE SUBMIT
        |--------------------------------------------------------------------------
        */

        prepareSubmit(event) {

            /*
            |--------------------------------------------------------------------------
            | OPEN INVOICE
            |--------------------------------------------------------------------------
            */

            if (this.activeOpenInvoiceId) {

                if (
                    this.pendingItems.length > 0
                ) {

                    event.preventDefault();

                    alert(
                        'Ambil barang baru terlebih dahulu sebelum melakukan pembayaran.'
                    );

                    return;

                }

                if (
                    this.paymentMethod !== 'cash' &&
                    this.paymentMethod !== 'qris'
                ) {

                    event.preventDefault();

                    alert(
                        'Open Invoice hanya dapat dibayar dengan Cash atau QRIS.'
                    );

                    return;

                }

                const payment =
                    Number(this.paid) || 0;

                const remaining =
                    Number(
                        this.openInvoiceRemaining
                    ) || 0;

                if (
                    payment <= 0
                ) {

                    event.preventDefault();

                    alert(
                        'Masukkan nominal pembayaran.'
                    );

                    return;

                }

                /*
                 * QRIS TIDAK BOLEH LEBIH
                 * DARI SISA TAGIHAN.
                 *
                 * CASH BOLEH LEBIH.
                 * Kelebihan menjadi kembalian.
                 */

                if (
                    this.paymentMethod === 'qris' &&
                    payment > remaining
                ) {

                    event.preventDefault();

                    alert(
                        'Nominal QRIS tidak boleh melebihi sisa tagihan.'
                    );

                    return;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | TRANSAKSI NORMAL
            |--------------------------------------------------------------------------
            */

            if (!this.activeOpenInvoiceId) {

                if (
                    this.cart.length === 0
                ) {

                    event.preventDefault();

                    alert(
                        'Keranjang masih kosong.'
                    );

                    return;

                }

                if (
                    this.paymentMethod !== 'credit' &&
                    Number(this.paid) <
                    Number(this.total)
                ) {

                    event.preventDefault();

                    alert(
                        'Uang pembayaran kurang.'
                    );

                    return;

                }

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

                    this.paid =
                        0;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | PREPARE ITEMS
            |--------------------------------------------------------------------------
            */

            const container =
                document.getElementById(
                    'checkout-items'
                );

            if (!container) {

                event.preventDefault();

                alert(
                    'Form transaksi tidak ditemukan.'
                );

                return;

            }

            container.innerHTML =
                '';


            this.cart.forEach(
                (item, index) => {

                    const productId =
                        document.createElement(
                            'input'
                        );

                    productId.type =
                        'hidden';

                    productId.name =
                        `items[${index}][product_id]`;

                    productId.value =
                        item.product_id;


                    const variantId =
                        document.createElement(
                            'input'
                        );

                    variantId.type =
                        'hidden';

                    variantId.name =
                        `items[${index}][product_variant_id]`;

                    variantId.value =
                        item.product_variant_id ?? '';


                    const quantity =
                        document.createElement(
                            'input'
                        );

                    quantity.type =
                        'hidden';

                    quantity.name =
                        `items[${index}][quantity]`;

                    quantity.value =
                        item.quantity;


                    container.appendChild(
                        productId
                    );

                    container.appendChild(
                        variantId
                    );

                    container.appendChild(
                        quantity
                    );

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

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

</style>

@endpush
