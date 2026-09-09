@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

        <div>
            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center shadow-sm">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 14.25l6-6m-6.75 0h.008v.008H8.25V8.25zm7.5 7.5h.008v.008h-.008v-.008z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M7.5 3.75h9A2.25 2.25 0 0118.75 6v12a2.25 2.25 0 01-2.25 2.25h-9A2.25 2.25 0 015.25 18V6A2.25 2.25 0 017.5 3.75z"
                        />
                    </svg>

                </div>

                <div>

                    <h2 class="text-xl font-bold text-gray-900">
                        Riwayat Transaksi
                    </h2>

                    <p class="text-sm text-gray-500 mt-0.5">
                        Kelola dan lihat seluruh transaksi penjualan
                    </p>

                </div>

            </div>
        </div>

    </div>


    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Jumlah Transaksi --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-500">
                        Jumlah Transaksi
                    </p>

                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $transactions->total() }}
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-gray-100 flex items-center justify-center">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-gray-700"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6M9 8h6m6 4a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>

                </div>

            </div>

        </div>


        {{-- Info --}}
        <div class="bg-gray-900 rounded-2xl p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-gray-400">
                        Status
                    </p>

                    <p class="text-lg font-bold text-white mt-1">
                        Transaksi Selesai
                    </p>

                </div>

                <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- Filter Card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100">

            <div class="flex items-center gap-2">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 text-gray-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 4.5h18M6 9.75h12M10 15h4M11 19.5h2"
                    />
                </svg>

                <h3 class="text-sm font-semibold text-gray-900">
                    Filter Transaksi
                </h3>

            </div>

        </div>


        <div class="p-5">

            <form
                action="{{ route('transactions.index') }}"
                method="GET"
                class="flex flex-col xl:flex-row xl:items-end gap-4"
            >

                {{-- Dari --}}
                <div class="w-full xl:w-52">

                    <label
                        for="date_from"
                        class="block text-xs font-semibold text-gray-600 mb-2"
                    >
                        Dari Tanggal
                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v12A1.5 1.5 0 0118.75 20.25H5.25a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z"
                                />
                            </svg>

                        </div>

                        <input
                            type="date"
                            name="date_from"
                            id="date_from"
                            value="{{ request('date_from') }}"
                            class="w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200
                                   text-sm text-gray-700 bg-gray-50
                                   focus:bg-white focus:border-gray-900
                                   focus:ring-1 focus:ring-gray-900"
                        >

                    </div>

                </div>


                {{-- Sampai --}}
                <div class="w-full xl:w-52">

                    <label
                        for="date_to"
                        class="block text-xs font-semibold text-gray-600 mb-2"
                    >
                        Sampai Tanggal
                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3.75 9h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 18.75v1.5H5.25a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z"
                                />
                            </svg>

                        </div>

                        <input
                            type="date"
                            name="date_to"
                            id="date_to"
                            value="{{ request('date_to') }}"
                            class="w-full pl-10 pr-3 py-2.5 rounded-xl border-gray-200
                                   text-sm text-gray-700 bg-gray-50
                                   focus:bg-white focus:border-gray-900
                                   focus:ring-1 focus:ring-gray-900"
                        >

                    </div>

                </div>


                {{-- Filter --}}
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2
                           px-5 py-2.5 rounded-xl bg-gray-900 text-white
                           text-sm font-semibold hover:bg-gray-800
                           transition shadow-sm"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 4.5h18M6 9.75h12M10 15h4M11 19.5h2"
                        />
                    </svg>

                    Terapkan Filter

                </button>


                {{-- Reset --}}
                @if(request('date_from') || request('date_to'))

                    <a
                        href="{{ route('transactions.index') }}"
                        class="inline-flex items-center justify-center gap-2
                               px-5 py-2.5 rounded-xl border border-gray-200
                               text-sm font-semibold text-gray-600
                               hover:bg-gray-50 transition"
                    >

                        Reset

                    </a>

                @endif


                {{-- PDF --}}
                <a
                    href="{{ route('transactions.pdf', request()->query()) }}"
                    target="_blank"
                    class="xl:ml-auto inline-flex items-center justify-center gap-2
                           px-5 py-2.5 rounded-xl border border-gray-900
                           text-gray-900 bg-white text-sm font-semibold
                           hover:bg-gray-900 hover:text-white
                           transition"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 9V4.5A1.5 1.5 0 017.5 3h9A1.5 1.5 0 0118 4.5V9M6 18H4.5A1.5 1.5 0 013 16.5v-6A1.5 1.5 0 014.5 9h15a1.5 1.5 0 011.5 1.5v6a1.5 1.5 0 01-1.5 1.5H18M6 14h12v7H6v-7z"
                        />
                    </svg>

                    Cetak PDF

                </a>

            </form>

        </div>

    </div>


    {{-- Transaction Table --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

            <div>

                <h3 class="text-sm font-semibold text-gray-900">
                    Daftar Transaksi
                </h3>

                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $transactions->total() }} transaksi ditemukan
                </p>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>

                    <tr class="bg-gray-50/80 border-b border-gray-200">

                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 text-xs uppercase tracking-wide">
                            Invoice
                        </th>

                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 text-xs uppercase tracking-wide">
                            Tanggal
                        </th>

                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 text-xs uppercase tracking-wide">
                            Kasir
                        </th>

                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 text-xs uppercase tracking-wide">
                            Pembayaran
                        </th>

                        <th class="text-right px-5 py-3.5 font-semibold text-gray-500 text-xs uppercase tracking-wide">
                            Total
                        </th>

                        <th class="text-right px-5 py-3.5 font-semibold text-gray-500 text-xs uppercase tracking-wide">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse ($transactions as $transaction)

                        <tr class="hover:bg-gray-50/70 transition">

                            {{-- Invoice --}}
                            <td class="px-5 py-4">

                                <div class="font-semibold text-gray-900">
                                    {{ $transaction->invoice_number }}
                                </div>

                            </td>


                            {{-- Tanggal --}}
                            <td class="px-5 py-4">

                                <div class="text-gray-700">
                                    {{ $transaction->created_at->format('d/m/Y') }}
                                </div>

                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $transaction->created_at->format('H:i') }}
                                </div>

                            </td>


                            {{-- Kasir --}}
                            <td class="px-5 py-4">

                                <div class="flex items-center gap-2.5">

                                    <div class="w-8 h-8 rounded-full bg-gray-100
                                                flex items-center justify-center
                                                text-xs font-bold text-gray-600">

                                        {{ strtoupper(substr($transaction->user->name, 0, 1)) }}

                                    </div>

                                    <span class="text-gray-700 font-medium">
                                        {{ $transaction->user->name }}
                                    </span>

                                </div>

                            </td>


                            {{-- Pembayaran --}}
                            <td class="px-5 py-4">

                                @php
                                    $paymentStyle = match ($transaction->payment_method) {
                                        'cash' => 'bg-green-50 text-green-700 ring-green-600/10',
                                        'qris' => 'bg-blue-50 text-blue-700 ring-blue-600/10',
                                        'credit' => 'bg-orange-50 text-orange-700 ring-orange-600/10',
                                        default => 'bg-gray-50 text-gray-600 ring-gray-500/10',
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center px-2.5 py-1
                                           rounded-full text-xs font-semibold
                                           ring-1 ring-inset {{ $paymentStyle }}"
                                >
                                    {{ strtoupper($transaction->payment_method) }}
                                </span>

                            </td>


                            {{-- Total --}}
                            <td class="px-5 py-4 text-right">

                                <span class="font-bold text-gray-900">
                                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                </span>

                            </td>


                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right">

                                <a
                                    href="{{ route('pos.receipt', $transaction) }}"
                                    class="inline-flex items-center gap-1.5
                                           px-3 py-1.5 rounded-lg
                                           text-xs font-semibold text-gray-700
                                           border border-gray-200
                                           hover:bg-gray-900 hover:text-white
                                           hover:border-gray-900 transition"
                                >

                                    Lihat Invoice

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-3.5 h-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 5l7 7-7 7"
                                        />
                                    </svg>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-16 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="w-12 h-12 rounded-full bg-gray-100
                                                flex items-center justify-center mb-3">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-6 h-6 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="1.6"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 14.25l6-6m-6.75 0h.008v.008H8.25V8.25zm7.5 7.5h.008v.008h-.008v-.008z"
                                            />
                                        </svg>

                                    </div>

                                    <p class="font-semibold text-gray-700">
                                        Belum ada transaksi
                                    </p>

                                    <p class="text-sm text-gray-400 mt-1">
                                        Tidak ada transaksi pada periode yang dipilih.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($transactions->hasPages())

            <div class="px-5 py-4 border-t border-gray-100">

                {{ $transactions->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
