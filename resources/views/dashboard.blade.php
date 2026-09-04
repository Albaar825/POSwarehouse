@extends('layouts.app')

@section('title', 'Dashboard')

@push('head')
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    @stack('head')
@endpush

@section('content')

<div class="dashboard-wrapper">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">
                Dashboard
            </h1>

            <p class="dashboard-subtitle">
                Selamat datang kembali, {{ auth()->user()->name }} 👋
            </p>
        </div>

        <div class="dashboard-date">
            <span>Hari ini</span>
            <strong>{{ now()->translatedFormat('l, d F Y') }}</strong>
        </div>
    </div>


    {{-- ============================================================= --}}
    {{-- ADMIN --}}
    {{-- ============================================================= --}}

    @if (auth()->user()->isAdmin())

        {{-- SUMMARY CARDS --}}
        <div class="dashboard-stats">

            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    📦
                </div>

                <div>
                    <p>Total Produk</p>
                    <h3>{{ number_format($total_products) }}</h3>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-icon stat-icon-orange">
                    📊
                </div>

                <div>
                    <p>Total Stok</p>
                    <h3>{{ number_format($total_stock) }}</h3>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-icon stat-icon-red">
                    ⚠️
                </div>

                <div>
                    <p>Stok Menipis</p>
                    <h3 class="text-red-600">
                        {{ number_format($low_stock_count) }}
                    </h3>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    💰
                </div>

                <div>
                    <p>Penjualan Hari Ini</p>
                    <h3>
                        Rp {{ number_format($today_sales, 0, ',', '.') }}
                    </h3>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    🧾
                </div>

                <div>
                    <p>Transaksi Hari Ini</p>
                    <h3>
                        {{ number_format($today_transactions) }}
                    </h3>
                </div>
            </div>


            <div class="stat-card">
                <div class="stat-icon stat-icon-gray">
                    📅
                </div>

                <div>
                    <p>Periode Grafik</p>
                    <h3>7 Hari</h3>
                </div>
            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- CHARTS --}}
        {{-- ============================================================= --}}

        <div class="dashboard-chart-grid">

            {{-- SALES --}}
            <div class="dashboard-card chart-card chart-large">

                <div class="card-header">
                    <div>
                        <h2>Penjualan</h2>
                        <p>Omzet 7 hari terakhir</p>
                    </div>

                    <span class="chart-badge">
                        7 Hari
                    </span>
                </div>

                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>

            </div>


            {{-- TRANSACTIONS --}}
            <div class="dashboard-card chart-card">

                <div class="card-header">
                    <div>
                        <h2>Transaksi</h2>
                        <p>Jumlah transaksi harian</p>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="transactionChart"></canvas>
                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- CATEGORY + TOP PRODUCTS --}}
        {{-- ============================================================= --}}

        <div class="dashboard-chart-grid">

            <div class="dashboard-card chart-card">

                <div class="card-header">
                    <div>
                        <h2>Penjualan Kategori</h2>
                        <p>30 hari terakhir</p>
                    </div>
                </div>

                <div class="category-chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>

            </div>


            <div class="dashboard-card chart-card">

                <div class="card-header">
                    <div>
                        <h2>Produk Terlaris</h2>
                        <p>30 hari terakhir</p>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="topProductChart"></canvas>
                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- LOW STOCK + RECENT TRANSACTIONS --}}
        {{-- ============================================================= --}}

        <div class="dashboard-content-grid">

            {{-- LOW STOCK --}}
            <div class="dashboard-card">

                <div class="card-header">
                    <div>
                        <h2>Stok Menipis</h2>
                        <p>Produk yang perlu diperhatikan</p>
                    </div>

                    <a href="{{ route('products.index') }}">
                        Lihat Semua
                    </a>
                </div>


                <div class="dashboard-list">

                    @forelse ($low_stock_products as $product)

                        <div class="dashboard-list-item">

                            <div class="product-info">

                                <div class="product-avatar">
                                    @if ($product->image)
                                        <img
                                            src="{{ $product->imageUrl() }}"
                                            alt="{{ $product->name }}"
                                        >
                                    @else
                                        📦
                                    @endif
                                </div>

                                <div>
                                    <strong>
                                        {{ $product->name }}
                                    </strong>

                                    <span>
                                        {{ $product->sku }}
                                    </span>
                                </div>

                            </div>


                            <div class="stock-warning">

                                <strong>
                                    {{ $product->totalStock() }}
                                </strong>

                                <span>
                                    / min {{ $product->min_stock }}
                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">
                            <span>✓</span>
                            <p>Semua stok masih aman</p>
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- RECENT TRANSACTIONS --}}
            <div class="dashboard-card">

                <div class="card-header">
                    <div>
                        <h2>Transaksi Terbaru</h2>
                        <p>Aktivitas penjualan terakhir</p>
                    </div>

                    <a href="{{ route('transactions.index') }}">
                        Lihat Semua
                    </a>
                </div>


                <div class="dashboard-list">

                    @forelse ($recent_transactions as $transaction)

                        <div class="dashboard-list-item">

                            <div>
                                <strong>
                                    {{ $transaction->invoice_number }}
                                </strong>

                                <span>
                                    {{ $transaction->user->name ?? '-' }}
                                    •
                                    {{ $transaction->created_at->format('d/m H:i') }}
                                </span>
                            </div>


                            <div class="transaction-total">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </div>

                        </div>

                    @empty

                        <div class="empty-state">
                            <span>🧾</span>
                            <p>Belum ada transaksi</p>
                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        {{-- ============================================================= --}}
        {{-- STOCK MOVEMENT + OPNAME --}}
        {{-- ============================================================= --}}

        <div class="dashboard-content-grid">

            {{-- STOCK MOVEMENT --}}
            <div class="dashboard-card">

                <div class="card-header">
                    <div>
                        <h2>Aktivitas Stok</h2>
                        <p>Pergerakan stok terbaru</p>
                    </div>

                    <a href="{{ route('stock.index') }}">
                        Lihat Semua
                    </a>
                </div>


                <div class="dashboard-list">

                    @forelse ($recent_stock_movements as $movement)

                        <div class="dashboard-list-item">

                            <div>
                                <strong>
                                    {{ $movement->product->name ?? '-' }}
                                </strong>

                                <span>
                                    {{ $movement->source }}
                                    •
                                    {{ $movement->created_at->format('d/m H:i') }}
                                </span>
                            </div>


                            <div
                                class="{{ $movement->type === 'in'
                                    ? 'movement-in'
                                    : 'movement-out' }}"
                            >

                                {{ $movement->type === 'in' ? '+' : '-' }}
                                {{ $movement->quantity }}

                            </div>

                        </div>

                    @empty

                        <div class="empty-state">
                            <span>📦</span>
                            <p>Belum ada aktivitas stok</p>
                        </div>

                    @endforelse

                </div>

            </div>


            {{-- STOCK OPNAME --}}
            <div class="dashboard-card">

                <div class="card-header">
                    <div>
                        <h2>Stock Opname</h2>
                        <p>Riwayat opname terbaru</p>
                    </div>

                    <a href="{{ route('admin.stock-opname.index') }}">
                        Lihat Semua
                    </a>
                </div>


                <div class="dashboard-list">

                    @forelse ($recent_opnames as $opname)

                        <div class="dashboard-list-item">

                            <div>
                                <strong>
                                    Opname #{{ $opname->id }}
                                </strong>

                                <span>
                                    {{ $opname->opname_date->format('d M Y') }}
                                    •
                                    {{ $opname->user->name ?? '-' }}
                                </span>
                            </div>


                            <span class="status-completed">
                                {{ ucfirst($opname->status) }}
                            </span>

                        </div>

                    @empty

                        <div class="empty-state">
                            <span>📋</span>
                            <p>Belum ada stock opname</p>
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    @endif


    {{-- ============================================================= --}}
    {{-- KASIR --}}
    {{-- ============================================================= --}}

    @if (auth()->user()->isKasir())

        <div class="kasir-dashboard">

            <div class="kasir-welcome">
                <div>
                    <span>Selamat datang 👋</span>

                    <h2>
                        {{ auth()->user()->name }}
                    </h2>

                    <p>
                        Siap melayani transaksi hari ini?
                    </p>
                </div>

                <a href="{{ route('pos.index') }}">
                    Buka Kasir →
                </a>
            </div>


            <div class="stat-card">

                <div class="stat-icon stat-icon-purple">
                    🧾
                </div>

                <div>
                    <p>Transaksi Hari Ini</p>

                    <h3>
                        {{ $my_transactions_today }}
                    </h3>
                </div>

            </div>

        </div>

    @endif

</div>

@endsection


@if (auth()->user()->isAdmin())

    @push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
        document.addEventListener('DOMContentLoaded', function () {

            const salesCanvas = document.getElementById('salesChart');

            if (salesCanvas) {
                new Chart(salesCanvas, {
                    type: 'line',

                    data: {
                        labels: @json($sales_labels),

                        datasets: [{
                            label: 'Penjualan',
                            data: @json($sales_data),
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },

                        plugins: {
                            legend: {
                                display: false
                            },

                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return 'Rp ' +
                                            new Intl.NumberFormat('id-ID')
                                                .format(context.raw);
                                    }
                                }
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    callback: function (value) {
                                        return 'Rp ' +
                                            new Intl.NumberFormat('id-ID')
                                                .format(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }


            const transactionCanvas =
                document.getElementById('transactionChart');

            if (transactionCanvas) {
                new Chart(transactionCanvas, {
                    type: 'bar',

                    data: {
                        labels: @json($sales_labels),

                        datasets: [{
                            label: 'Transaksi',
                            data: @json($transaction_data),
                            borderRadius: 6
                        }]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }


            const categoryCanvas =
                document.getElementById('categoryChart');

            if (categoryCanvas) {
                new Chart(categoryCanvas, {
                    type: 'doughnut',

                    data: {
                        labels: @json($category_labels),

                        datasets: [{
                            data: @json($category_data),
                            borderWidth: 2
                        }]
                    },

                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',

                        plugins: {
                            legend: {
                                position: 'bottom'
                            },

                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return 'Rp ' +
                                            new Intl.NumberFormat('id-ID')
                                                .format(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });
            }


            const topProductCanvas =
                document.getElementById('topProductChart');

            if (topProductCanvas) {
                new Chart(topProductCanvas, {
                    type: 'bar',

                    data: {
                        labels: @json($top_product_labels),

                        datasets: [{
                            label: 'Terjual',
                            data: @json($top_product_data),
                            borderRadius: 6
                        }]
                    },

                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,

                        plugins: {
                            legend: {
                                display: false
                            }
                        },

                        scales: {
                            x: {
                                beginAtZero: true,

                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

        });
        </script>

    @endpush

@endif
