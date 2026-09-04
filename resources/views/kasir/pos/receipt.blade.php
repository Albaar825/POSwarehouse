@extends('layouts.app')

@section('title', 'Invoice')

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-5">

        <div>
            <h2 class="text-xl font-bold text-gray-900">
                Transaksi Berhasil
            </h2>

            <p class="text-sm text-gray-500">
                Invoice berhasil dibuat
            </p>
        </div>

        <div class="flex gap-2">

            <a
                href="{{ route('pos.index') }}"
                class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50"
            >
                Transaksi Baru
            </a>

            <button
                onclick="window.print()"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800"
            >
                Cetak
            </button>

        </div>

    </div>


    <div id="receipt" class="bg-white border border-gray-200 rounded-xl p-6">

        {{-- HEADER --}}
        <div class="text-center border-b border-dashed border-gray-300 pb-5">

            <h1 class="text-xl font-bold">
                KASIR & WAREHOUSE
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Invoice Penjualan
            </p>

        </div>


        {{-- INFO --}}
        <div class="grid grid-cols-2 gap-4 py-5 text-sm">

            <div>

                <p class="text-gray-500">
                    Invoice
                </p>

                <p class="font-semibold">
                    {{ $transaction->invoice_number }}
                </p>

            </div>

            <div class="text-right">

                <p class="text-gray-500">
                    Tanggal
                </p>

                <p class="font-semibold">
                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                </p>

            </div>

            <div>

                <p class="text-gray-500">
                    Kasir
                </p>

                <p class="font-semibold">
                    {{ $transaction->user->name }}
                </p>

            </div>

            <div class="text-right">

                <p class="text-gray-500">
                    Pembayaran
                </p>

                <p class="font-semibold uppercase">
                    {{ $transaction->payment_method }}
                </p>

            </div>

        </div>


        {{-- ITEMS --}}
        <div class="border-t border-b border-gray-200 py-4">

            <div class="space-y-4">

                @foreach ($transaction->items as $item)

                    <div class="flex justify-between gap-4">

                        <div class="min-w-0">

                            <p class="font-semibold text-sm">
                                {{ $item->product_name }}
                            </p>

                            @if ($item->variant_label)

                                <p class="text-xs text-gray-500">
                                    {{ $item->variant_label }}
                                </p>

                            @endif

                            <p class="text-xs text-gray-500">
                                {{ $item->quantity }} ×
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </p>

                        </div>

                        <p class="font-semibold text-sm whitespace-nowrap">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- TOTAL --}}
        <div class="py-5 space-y-2 text-sm">

            <div class="flex justify-between">

                <span class="text-gray-500">
                    Total
                </span>

                <span class="font-bold text-lg">
                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                </span>

            </div>

            <div class="flex justify-between">

                <span class="text-gray-500">
                    Dibayar
                </span>

                <span>
                    Rp {{ number_format($transaction->paid, 0, ',', '.') }}
                </span>

            </div>

            <div class="flex justify-between">

                <span class="text-gray-500">
                    Kembalian
                </span>

                <span class="font-semibold">
                    Rp {{ number_format($transaction->change, 0, ',', '.') }}
                </span>

            </div>

        </div>


        <div class="text-center border-t border-dashed border-gray-300 pt-5">

            <p class="text-sm font-medium">
                Terima kasih atas kunjungan Anda
            </p>

            <p class="text-xs text-gray-500 mt-1">
                Barang yang sudah dibeli tidak dapat dikembalikan
            </p>

        </div>

    </div>

</div>


<style>

@media print {

    body {
        background: white !important;
    }

    body * {
        visibility: hidden;
    }

    #receipt,
    #receipt * {
        visibility: visible;
    }

    #receipt {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none;
        box-shadow: none;
    }

}

</style>

@endsection
