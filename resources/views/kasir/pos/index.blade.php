@extends('layouts.app')

@section('title', 'Point of Sale')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
@endpush

@section('content')

<div
    x-data="posApp()"
    class="pos-page"
>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="pos-header">

        <div>
            <div class="pos-title-row">
                <div class="pos-title-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 10h18M5 10v9h14v-9M4 6h16l1 4H3l1-4Z"
                        />
                    </svg>
                </div>

                <div>
                    <h1 class="pos-title">
                        Point of Sale
                    </h1>

                    <p class="pos-subtitle">
                        Kelola transaksi penjualan dengan mudah
                    </p>
                </div>
            </div>
        </div>

        <div class="pos-cashier">
            <span class="pos-cashier-label">
                Kasir
            </span>

            <div class="pos-cashier-user">
                <div class="pos-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div>
                    <p class="pos-cashier-name">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="pos-cashier-role">
                        Kasir
                    </p>
                </div>
            </div>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SEARCH --}}
    {{-- ========================================================= --}}

    <div class="pos-search-card">

        <div class="pos-search">

            <svg
                class="pos-search-icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
            >
                <circle
                    cx="11"
                    cy="11"
                    r="7"
                    stroke-width="1.8"
                />

                <path
                    stroke-linecap="round"
                    stroke-width="1.8"
                    d="m20 20-4-4"
                />
            </svg>

            <input
                type="text"
                x-model="search"
                placeholder="Cari nama produk atau SKU..."
            >

            <div class="pos-search-shortcut">
                <span>⌘</span>
                <span>K</span>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MAIN CONTENT --}}
    {{-- ========================================================= --}}

    <div class="pos-layout">


        {{-- ===================================================== --}}
        {{-- PRODUCT SECTION --}}
        {{-- ===================================================== --}}

        <section class="pos-products-section">

            <div class="pos-section-header">

                <div>
                    <h2 class="pos-section-title">
                        Produk
                    </h2>

                    <p class="pos-section-description">
                        Pilih produk yang ingin dijual
                    </p>
                </div>

                <div class="pos-product-count">
                    <span>
                        {{ $products->count() }}
                    </span>
                    Produk
                </div>

            </div>


            {{-- PRODUCT LIST --}}

            <div class="pos-product-area">

                @if ($products->count())

                    <div class="pos-product-list">

                        @foreach ($products as $product)

                            @php
                                $productJson = $productsJson->firstWhere(
                                    'id',
                                    $product->id
                                );

                                $productImage = $productJson['image'] ?? null;
                            @endphp

                            <button
                                type="button"
                                @click="openVariant({{ $product->id }})"
                                x-show="matches(
                                    '{{ strtolower(addslashes($product->name)) }}',
                                    '{{ strtolower(addslashes($product->sku)) }}'
                                )"
                                class="pos-product-card-horizontal"
                            >

                                {{-- IMAGE --}}

                                <div class="pos-product-h-image">

                                    @if ($productImage)

                                        <img
                                            src="{{ $productImage }}"
                                            alt="{{ $product->name }}"
                                        >

                                    @else

                                        <div class="pos-no-image-h">

                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <rect
                                                    x="3"
                                                    y="4"
                                                    width="18"
                                                    height="16"
                                                    rx="2"
                                                    stroke-width="1.5"
                                                />

                                                <circle
                                                    cx="8"
                                                    cy="9"
                                                    r="1.5"
                                                    stroke-width="1.5"
                                                />

                                                <path
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="m4 17 4-4 3 3 2-2 5 5"
                                                />
                                            </svg>

                                        </div>

                                    @endif

                                    {{-- CART BADGE --}}

                                    <span class="pos-cart-badge-h">
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 4a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                    </span>

                                </div>


                                {{-- INFO --}}

                                <div class="pos-product-h-content">

                                    <div class="pos-product-h-header">

                                        <p class="pos-product-h-name">
                                            {{ $product->name }}
                                        </p>

                                        <p class="pos-product-h-price">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>

                                    </div>


                                    <div class="pos-product-h-footer">

                                        <div>
                                            <p class="pos-product-h-sku">
                                                SKU {{ $product->sku }}
                                            </p>

                                            <p class="pos-product-h-stock">
                                                Stok {{ $product->stock }} {{ $product->unit }}
                                            </p>
                                        </div>

                                        <span class="pos-add-btn-h">
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 5v14M5 12h14"
                                                />
                                            </svg>
                                        </span>

                                    </div>

                                </div>

                            </button>

                        @endforeach

                    </div>

                    {{-- NO SEARCH RESULT --}}

                    <div
                        x-show="search && !hasVisibleProducts()"
                        x-cloak
                        class="pos-empty-products"
                    >

                        <div class="pos-empty-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <circle
                                    cx="11"
                                    cy="11"
                                    r="7"
                                    stroke-width="1.5"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-width="1.5"
                                    d="m20 20-4-4"
                                />
                            </svg>
                        </div>

                        <h3>
                            Produk tidak ditemukan
                        </h3>

                        <p>
                            Coba gunakan nama produk atau SKU yang berbeda.
                        </p>

                    </div>

                @else

                    <div class="pos-empty-products">

                        <div class="pos-empty-icon">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="16"
                                    rx="2"
                                    stroke-width="1.5"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-width="1.5"
                                    d="M8 9h8M8 13h5"
                                />
                            </svg>
                        </div>

                        <h3>
                            Tidak ada produk
                        </h3>

                        <p>
                            Belum ada produk yang tersedia untuk transaksi.
                        </p>

                    </div>

                @endif

            </div>

        </section>


        {{-- ===================================================== --}}
        {{-- CART --}}
        {{-- ===================================================== --}}

        <aside class="pos-cart-card">

            {{-- CART HEADER --}}

            <div class="pos-cart-header">

                <div class="pos-cart-title-wrapper">

                    <div class="pos-cart-icon">
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.7"
                                d="M3 4h2l1.5 10h11L21 7H6"
                            />

                            <circle
                                cx="9"
                                cy="19"
                                r="1.3"
                                stroke-width="1.7"
                            />

                            <circle
                                cx="18"
                                cy="19"
                                r="1.3"
                                stroke-width="1.7"
                            />
                        </svg>
                    </div>

                    <div>
                        <h2>
                            Keranjang
                        </h2>

                        <p>
                            <span x-text="cart.length"></span>
                            item
                        </p>
                    </div>

                </div>

                <button
                    type="button"
                    x-show="cart.length"
                    x-cloak
                    @click="clearCart()"
                    class="pos-clear-cart"
                >
                    Kosongkan
                </button>

            </div>


            {{-- CART ITEMS --}}

            <div class="pos-cart-items">

                <template x-if="cart.length === 0">

                    <div class="pos-empty-cart">

                        <div class="pos-empty-cart-icon">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="M3 4h2l1.5 10h11L21 7H6"
                                />

                                <circle
                                    cx="9"
                                    cy="19"
                                    r="1.3"
                                    stroke-width="1.6"
                                />

                                <circle
                                    cx="18"
                                    cy="19"
                                    r="1.3"
                                    stroke-width="1.6"
                                />
                            </svg>

                        </div>

                        <h3>
                            Keranjang masih kosong
                        </h3>

                        <p>
                            Pilih produk untuk memulai transaksi.
                        </p>

                    </div>

                </template>


                <div class="pos-cart-list">

                    <template
                        x-for="(item, index) in cart"
                        :key="item.key"
                    >

                        <div class="pos-cart-item">

                            <div class="pos-cart-item-main">

                                <div class="pos-cart-image">

                                    <img
                                        :src="item.image"
                                        alt=""
                                    >

                                </div>

                                <div class="pos-cart-info">

                                    <p
                                        class="pos-cart-item-name"
                                        x-text="item.name"
                                    ></p>

                                    <p
                                        class="pos-cart-item-variant"
                                        x-text="item.variant"
                                    ></p>

                                    <p
                                        class="pos-cart-item-price"
                                        x-text="formatRupiah(item.price)"
                                    ></p>

                                </div>

                                <button
                                    type="button"
                                    @click="removeItem(index)"
                                    class="pos-remove-item"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.7"
                                            d="M6 7h12M10 11v6M14 11v6M8 7l1-3h6l1 3m2 0-1 13H7L6 7"
                                        />
                                    </svg>
                                </button>

                            </div>


                            <div class="pos-cart-item-bottom">

                                <div class="pos-quantity">

                                    <button
                                        type="button"
                                        @click="decrease(index)"
                                    >
                                        −
                                    </button>

                                    <span
                                        x-text="item.quantity"
                                    ></span>

                                    <button
                                        type="button"
                                        @click="increase(index)"
                                    >
                                        +
                                    </button>

                                </div>

                                <p
                                    class="pos-cart-item-total"
                                    x-text="formatRupiah(item.price * item.quantity)"
                                ></p>

                            </div>

                        </div>

                    </template>

                </div>

            </div>


            {{-- CHECKOUT --}}

            <div class="pos-checkout">

                {{-- TOTAL --}}

                <div class="pos-total-row">

                    <div>
                        <span class="pos-total-label">
                            Total Pembayaran
                        </span>

                        <span class="pos-total-items">
                            <span x-text="cart.length"></span>
                            item
                        </span>
                    </div>

                    <span
                        class="pos-total-price"
                        x-text="formatRupiah(total)"
                    ></span>

                </div>


                <div class="pos-divider"></div>


                <form
                    method="POST"
                    action="{{ route('pos.store') }}"
                    @submit="prepareSubmit($event)"
                >

                    @csrf

                    <div id="checkout-items"></div>


                    {{-- PAYMENT METHOD --}}

                    <div class="pos-form-group">

                        <label class="pos-form-label">
                            Metode Pembayaran
                        </label>

                        <div class="pos-payment-grid">

                            {{-- CASH --}}

                            <button
                                type="button"
                                @click="paymentMethod = 'cash'; paid = 0"
                                :class="paymentMethod === 'cash'
                                    ? 'pos-payment-active'
                                    : ''"
                                class="pos-payment-button"
                            >
                                <span class="pos-payment-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <rect
                                            x="3"
                                            y="6"
                                            width="18"
                                            height="12"
                                            rx="2"
                                            stroke-width="1.6"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                            stroke-width="1.6"
                                        />
                                    </svg>
                                </span>

                                Cash
                            </button>


                            {{-- QRIS --}}

                            <button
                                type="button"
                                @click="paymentMethod = 'qris'; paid = total"
                                :class="paymentMethod === 'qris'
                                    ? 'pos-payment-active'
                                    : ''"
                                class="pos-payment-button"
                            >
                                <span class="pos-payment-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <rect
                                            x="4"
                                            y="4"
                                            width="6"
                                            height="6"
                                            stroke-width="1.6"
                                        />

                                        <rect
                                            x="14"
                                            y="4"
                                            width="6"
                                            height="6"
                                            stroke-width="1.6"
                                        />

                                        <rect
                                            x="4"
                                            y="14"
                                            width="6"
                                            height="6"
                                            stroke-width="1.6"
                                        />

                                        <path
                                            stroke-width="1.6"
                                            d="M14 14h2v2h-2zM18 14h2v6h-6v-2M18 18h2"
                                        />
                                    </svg>
                                </span>

                                QRIS
                            </button>


                            {{-- CREDIT --}}

                            <button
                                type="button"
                                @click="paymentMethod = 'credit'; paid = 0"
                                :class="paymentMethod === 'credit'
                                    ? 'pos-payment-active'
                                    : ''"
                                class="pos-payment-button"
                            >
                                <span class="pos-payment-icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <rect
                                            x="3"
                                            y="5"
                                            width="18"
                                            height="14"
                                            rx="2"
                                            stroke-width="1.6"
                                        />

                                        <path
                                            stroke-width="1.6"
                                            d="M3 9h18M7 14h4"
                                        />
                                    </svg>
                                </span>

                                Kredit
                            </button>

                        </div>

                        <input
                            type="hidden"
                            name="payment_method"
                            :value="paymentMethod"
                        >

                    </div>


                    {{-- CREDIT CUSTOMER --}}

                    <div
                        x-show="paymentMethod === 'credit'"
                        x-cloak
                        class="pos-credit-box"
                    >

                        <div class="pos-credit-header">

                            <div class="pos-credit-icon">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                        d="M20 21a8 8 0 0 0-16 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h3>
                                    Informasi Customer
                                </h3>

                                <p>
                                    Lengkapi data untuk transaksi kredit
                                </p>
                            </div>

                        </div>


                        {{-- NAMA --}}

                        <div class="pos-input-group">

                            <label>
                                Nama Customer
                            </label>

                            <input
                                type="text"
                                name="customer_name"
                                x-model="customerName"
                                :required="paymentMethod === 'credit'"
                                placeholder="Masukkan nama customer"
                            >

                        </div>


                        {{-- PHONE --}}

                        <div class="pos-input-group">

                            <label>
                                No. HP Customer
                            </label>

                            <input
                                type="text"
                                name="customer_phone"
                                x-model="customerPhone"
                                :required="paymentMethod === 'credit'"
                                placeholder="Masukkan nomor HP customer"
                            >

                        </div>


                        {{-- DUE DATE --}}

                        <div class="pos-input-group">

                            <label>
                                Jatuh Tempo
                            </label>

                            <input
                                type="date"
                                name="due_date"
                                x-model="dueDate"
                                :required="paymentMethod === 'credit'"
                                min="{{ date('Y-m-d') }}"
                            >

                        </div>

                        <div class="pos-credit-notice">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke-width="1.7"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-width="1.7"
                                    d="M12 10v6M12 7h.01"
                                />
                            </svg>

                            <p>
                                Barang langsung mengurangi stok dan pembayaran
                                dapat dilakukan kemudian.
                            </p>

                        </div>

                    </div>


                    {{-- PAID --}}

                    <div
                        x-show="paymentMethod !== 'credit'"
                        class="pos-input-group pos-paid-group"
                    >

                        <div class="pos-paid-label-row">

                            <label>
                                Uang Dibayar
                            </label>

                            <span
                                x-show="paymentMethod === 'qris'"
                                class="pos-paid-auto"
                            >
                                Otomatis sesuai total
                            </span>

                        </div>

                        <div class="pos-money-input">

                            <span>
                                Rp
                            </span>

                            <input
                                type="number"
                                x-model.number="paid"
                                name="paid"
                                min="0"
                                :readonly="paymentMethod === 'qris'"
                                :value="paymentMethod === 'qris' ? total : paid"
                                placeholder="0"
                            >

                        </div>

                    </div>


                    {{-- CHANGE --}}

                    <div
                        x-show="
                            paymentMethod !== 'credit' &&
                            paid >= total &&
                            total > 0
                        "
                        x-cloak
                        class="pos-change-box"
                    >

                        <div class="pos-change-icon">
                            ✓
                        </div>

                        <div>
                            <span>
                                Kembalian
                            </span>

                            <strong
                                x-text="formatRupiah(Math.max(0, paid - total))"
                            ></strong>
                        </div>

                    </div>


                    {{-- CREDIT SUMMARY --}}

                    <div
                        x-show="
                            paymentMethod === 'credit' &&
                            total > 0
                        "
                        x-cloak
                        class="pos-credit-summary"
                    >

                        <span>
                            Sisa Tagihan
                        </span>

                        <strong
                            x-text="formatRupiah(total)"
                        ></strong>

                    </div>


                    {{-- SUBMIT --}}

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
                        class="pos-submit-button"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M5 12h14M13 6l6 6-6 6"
                            />
                        </svg>

                        <span x-show="paymentMethod !== 'credit'">
                            Bayar Sekarang
                        </span>

                        <span x-show="paymentMethod === 'credit'">
                            Simpan Transaksi Kredit
                        </span>

                    </button>

                </form>

            </div>

        </aside>

    </div>


    {{-- ========================================================= --}}
    {{-- VARIANT MODAL --}}
    {{-- ========================================================= --}}

    <div
        x-show="variantModal"
        x-cloak
        class="pos-modal"
    >

        <div
            class="pos-modal-overlay"
            @click="closeVariantModal()"
        ></div>


        <div class="pos-modal-content">

            {{-- MODAL HEADER --}}

            <div class="pos-modal-header">

                <div>
                    <span class="pos-modal-label">
                        Pilihan Produk
                    </span>

                    <h3>
                        Pilih Varian
                    </h3>

                    <p
                        x-text="selectedProduct ? selectedProduct.name : ''"
                    ></p>
                </div>

                <button
                    type="button"
                    @click="closeVariantModal()"
                    class="pos-modal-close"
                >
                    ×
                </button>

            </div>


            {{-- IMAGE --}}

            <div class="pos-modal-gallery">

                <div class="pos-main-image">

                    <template x-if="currentImages.length > 0">

                        <img
                            :src="currentImages[currentImageIndex]"
                            alt=""
                        >

                    </template>

                    <template x-if="currentImages.length === 0">

                        <div class="pos-modal-no-image">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >
                                <rect
                                    x="3"
                                    y="4"
                                    width="18"
                                    height="16"
                                    rx="2"
                                    stroke-width="1.5"
                                />

                                <path
                                    stroke-width="1.5"
                                    d="m4 17 5-5 3 3 2-2 6 6"
                                />
                            </svg>

                        </div>

                    </template>


                    {{-- PREVIOUS --}}

                    <button
                        type="button"
                        x-show="currentImages.length > 1"
                        @click="previousImage()"
                        class="pos-gallery-arrow pos-gallery-prev"
                    >
                        ‹
                    </button>


                    {{-- NEXT --}}

                    <button
                        type="button"
                        x-show="currentImages.length > 1"
                        @click="nextImage()"
                        class="pos-gallery-arrow pos-gallery-next"
                    >
                        ›
                    </button>

                </div>


                {{-- THUMBNAILS --}}

                <div
                    x-show="currentImages.length > 1"
                    class="pos-thumbnails"
                >

                    <template
                        x-for="(image, index) in currentImages"
                        :key="image + index"
                    >

                        <button
                            type="button"
                            @click="currentImageIndex = index"
                            :class="currentImageIndex === index
                                ? 'pos-thumbnail-active'
                                : ''"
                            class="pos-thumbnail"
                        >

                            <img
                                :src="image"
                                alt=""
                            >

                        </button>

                    </template>

                </div>

            </div>


            {{-- MODAL BODY --}}

            <div class="pos-modal-body">


                {{-- COLOR --}}

                <div class="pos-option-group">

                    <div class="pos-option-heading">

                        <label>
                            Warna
                        </label>

                        <span
                            x-show="selectedColor"
                            x-text="selectedColor"
                        ></span>

                    </div>

                    <div class="pos-color-options">

                        <template
                            x-for="color in availableColors"
                            :key="color"
                        >

                            <button
                                type="button"
                                @click="selectColor(color)"
                                :class="selectedColor === color
                                    ? 'pos-option-active'
                                    : ''"
                                class="pos-option-button"
                                x-text="color"
                            ></button>

                        </template>

                    </div>

                    <template x-if="availableColors.length === 0">

                        <p class="pos-option-empty">
                            Tidak ada pilihan warna.
                        </p>

                    </template>

                </div>


                {{-- SIZE --}}

                <div class="pos-option-group">

                    <div class="pos-option-heading">

                        <label>
                            Ukuran
                        </label>

                        <span
                            x-show="selectedSize"
                            x-text="selectedSize"
                        ></span>

                    </div>

                    <div class="pos-size-options">

                        <template
                            x-for="size in availableSizes"
                            :key="size"
                        >

                            <button
                                type="button"
                                @click="selectSize(size)"
                                :disabled="sizeStock(size) <= 0"
                                :class="
                                    selectedSize === size
                                        ? 'pos-option-active'
                                        : sizeStock(size) > 0
                                            ? ''
                                            : 'pos-option-disabled'
                                "
                                class="pos-size-button"
                                x-text="size"
                            ></button>

                        </template>

                    </div>

                    <template x-if="availableSizes.length === 0">

                        <p class="pos-option-empty">
                            Pilih warna terlebih dahulu.
                        </p>

                    </template>

                </div>


                {{-- SELECTED VARIANT --}}

                <template x-if="selectedVariant">

                    <div class="pos-selected-variant">

                        <div class="pos-variant-info">

                            <span>
                                Varian dipilih
                            </span>

                            <strong
                                x-text="selectedVariant.label"
                            ></strong>

                        </div>

                        <div class="pos-variant-stock">

                            <span>
                                Stok
                            </span>

                            <strong
                                x-text="selectedVariant.stock"
                            ></strong>

                        </div>

                        <div class="pos-variant-sku">

                            <span>
                                SKU Variant
                            </span>

                            <strong
                                x-text="selectedVariant.sku_variant || '-'"
                            ></strong>

                        </div>

                    </div>

                </template>


                {{-- ADD CART --}}

                <button
                    type="button"
                    @click="addSelectedVariant()"
                    :disabled="
                        !selectedVariant ||
                        Number(selectedVariant.stock) <= 0
                    "
                    class="pos-add-cart-button"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 5v14M5 12h14"
                        />
                    </svg>

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

        search: '',

        cart: [],

        variantModal: false,

        selectedProduct: null,

        selectedColor: null,

        selectedSize: null,

        selectedVariant: null,

        currentImages: [],

        currentImageIndex: 0,

        paymentMethod: 'cash',

        paid: 0,

        customerName: '',

        customerPhone: '',

        dueDate: '',

        products: @js($productsJson),


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


        hasVisibleProducts() {

            const keyword =
                this.search
                    .toLowerCase()
                    .trim();

            if (!keyword) {
                return true;
            }

            return this.products.some(product => {

                const name =
                    String(product.name)
                        .toLowerCase();

                const sku =
                    String(product.sku)
                        .toLowerCase();

                return (
                    name.includes(keyword) ||
                    sku.includes(keyword)
                );

            });

        },


        openVariant(productId) {

            this.selectedProduct =
                this.products.find(
                    product =>
                        Number(product.id) ===
                        Number(productId)
                );

            if (!this.selectedProduct) {
                return;
            }

            if (
                !Array.isArray(
                    this.selectedProduct.variants
                )
            ) {
                this.selectedProduct.variants = [];
            }

            this.selectedColor = null;
            this.selectedSize = null;
            this.selectedVariant = null;
            this.currentImageIndex = 0;

            this.currentImages =
                this.selectedProduct.image
                    ? [this.selectedProduct.image]
                    : [];

            this.variantModal = true;

            document.body.classList.add('pos-modal-open');

        },


        selectColor(color) {

            this.selectedColor = color;

            this.selectedSize = null;

            this.selectedVariant = null;

            this.currentImageIndex = 0;

            const colorKey =
                String(color)
                    .toLowerCase()
                    .trim();

            let images = [];

            if (
                this.selectedProduct.color_images &&
                Array.isArray(
                    this.selectedProduct.color_images[colorKey]
                )
            ) {

                images =
                    this.selectedProduct.color_images[colorKey];

            }

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

            this.currentImages = [
                ...new Set(
                    Array.isArray(images)
                        ? images
                        : []
                )
            ];

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

            if (!variant) {

                this.selectedVariant = null;

                return;

            }

            if (
                Number(variant.stock) <= 0
            ) {

                this.selectedVariant = null;

                return;

            }

            this.selectedSize = size;

            this.selectedVariant = variant;

        },


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
                                .trim() === colorKey &&

                            String(
                                variant.size
                            )
                                .toLowerCase()
                                .trim() === sizeKey
                        );

                    }
                );

            return variant
                ? Number(variant.stock)
                : 0;

        },


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


        addToCart(variant) {

            if (
                !this.selectedProduct ||
                !variant
            ) {
                return;
            }

            if (
                Number(variant.stock) <= 0
            ) {

                alert(
                    'Stok varian habis.'
                );

                return;

            }

            const key =
                this.selectedProduct.id +
                '-' +
                variant.id;

            const existing =
                this.cart.find(
                    item =>
                        item.key === key
                );

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

                let image =
                    this.selectedProduct.image;

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

            this.closeVariantModal();

        },


        closeVariantModal() {

            this.variantModal = false;

            this.selectedProduct = null;

            this.selectedColor = null;

            this.selectedSize = null;

            this.selectedVariant = null;

            this.currentImages = [];

            this.currentImageIndex = 0;

            document.body.classList.remove('pos-modal-open');

        },


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


        removeItem(index) {

            this.cart.splice(
                index,
                1
            );

        },


        clearCart() {

            this.cart = [];

            this.paid = 0;

            this.customerName = '';

            this.customerPhone = '';

            this.dueDate = '';

        },


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


        prepareSubmit(event) {

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
                this.paid < this.total
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

                this.paid = 0;

            }


            const container =
                document.getElementById(
                    'checkout-items'
                );

            container.innerHTML = '';


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

@endpush