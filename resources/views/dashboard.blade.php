@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-lg font-semibold mb-4">Selamat datang, {{ auth()->user()->name }} 👋</h1>

    @if (auth()->user()->isAdmin())
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500">Total Barang</p>
                <p class="text-xl font-semibold">{{ $total_products }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500">Stok Menipis</p>
                <p class="text-xl font-semibold text-red-600">{{ $low_stock_count }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-xs text-gray-500">Penjualan Hari Ini</p>
                <p class="text-xl font-semibold">Rp {{ number_format($today_sales, 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    @if (auth()->user()->isKasir())
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <p class="text-xs text-gray-500">Transaksi Hari Ini</p>
            <p class="text-xl font-semibold">{{ $my_transactions_today }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @if (auth()->user()->isAdmin())
            <a href="{{ route('products.index') }}" class="block bg-white rounded-lg shadow p-4 hover:shadow-md">
                <p class="font-medium text-gray-800">📦 Kelola Barang</p>
                <p class="text-sm text-gray-500">Data master barang</p>
            </a>
            <a href="{{ route('stock.index') }}" class="block bg-white rounded-lg shadow p-4 hover:shadow-md">
                <p class="font-medium text-gray-800">📊 Stok Masuk/Keluar</p>
                <p class="text-sm text-gray-500">Warehouse management</p>
            </a>
        @endif

        <a href="{{ route('pos.index') }}" class="block bg-white rounded-lg shadow p-4 hover:shadow-md">
            <p class="font-medium text-gray-800">🧾 Kasir (POS)</p>
            <p class="text-sm text-gray-500">Transaksi penjualan</p>
        </a>
        <a href="{{ route('transactions.index') }}" class="block bg-white rounded-lg shadow p-4 hover:shadow-md">
            <p class="font-medium text-gray-800">📋 Riwayat Transaksi</p>
            <p class="text-sm text-gray-500">Semua transaksi tercatat</p>
        </a>
    </div>
@endsection
