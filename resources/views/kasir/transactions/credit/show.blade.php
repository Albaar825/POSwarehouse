@extends('layouts.app')

@section('title', 'Detail Invoice Kredit')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>

            <div class="flex items-center gap-2 mb-2">

                <a
                    href="{{ route('pos.credit.index') }}"
                    class="text-gray-500 hover:text-gray-900"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <span class="text-sm text-gray-500">
                    Invoice Kredit
                </span>

            </div>

            <h1 class="text-2xl font-bold text-gray-900">
                {{ $transaction->invoice_number }}
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                {{ $transaction->created_at->format('d M Y H:i') }}
            </p>

        </div>

        @if($transaction->status === 'on_hold')

            <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-700">
                On Hold
            </span>

        @elseif($transaction->status === 'partial')

            <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-blue-100 text-blue-700">
                Partial Payment
            </span>

        @elseif($transaction->status === 'paid')

            <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium bg-green-100 text-green-700">
                Lunas
            </span>

        @endif

    </div>

    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm">
            {{ session('success') }}
        </div>

    @endif

    {{-- ERROR --}}
    @if($errors->any())

        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">

            <ul class="list-disc list-inside space-y-1">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif

    {{-- CUSTOMER + SUMMARY --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- CUSTOMER --}}
        <div class="lg:col-span-1 bg-white border border-gray-200 rounded-xl p-5">

            <h2 class="font-semibold text-gray-900 mb-4">
                Customer
            </h2>

            <div class="space-y-4">

                <div>

                    <div class="text-xs text-gray-500 mb-1">
                        Nama
                    </div>

                    <div class="font-medium text-gray-900">
                        {{ $transaction->customer->name ?? '-' }}
                    </div>

                </div>

                <div>

                    <div class="text-xs text-gray-500 mb-1">
                        Nomor HP
                    </div>

                    <div class="font-medium text-gray-900">
                        {{ $transaction->customer->phone ?? '-' }}
                    </div>

                </div>

                <div>

                    <div class="text-xs text-gray-500 mb-1">
                        Jatuh Tempo
                    </div>

                    @if($transaction->due_date)

                        <div class="
                            font-medium
                            {{ $transaction->due_date->isPast() && $transaction->status !== 'paid'
                                ? 'text-red-600'
                                : 'text-gray-900'
                            }}
                        ">
                            {{ $transaction->due_date->format('d M Y') }}
                        </div>

                    @else
                        <div class="text-gray-900">
                            -
                        </div>
                    @endif

                </div>

            </div>

        </div>

        {{-- SUMMARY --}}
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5">

            <h2 class="font-semibold text-gray-900 mb-4">
                Ringkasan Tagihan
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="bg-gray-50 rounded-lg p-4">

                    <div class="text-xs text-gray-500 mb-1">
                        Total Invoice
                    </div>

                    <div class="text-lg font-bold text-gray-900">
                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                    </div>

                </div>

                <div class="bg-green-50 rounded-lg p-4">

                    <div class="text-xs text-green-600 mb-1">
                        Sudah Dibayar
                    </div>

                    <div class="text-lg font-bold text-green-700">
                        Rp {{ number_format($transaction->paid, 0, ',', '.') }}
                    </div>

                </div>

                <div class="bg-red-50 rounded-lg p-4">

                    <div class="text-xs text-red-600 mb-1">
                        Sisa Tagihan
                    </div>

                    <div class="text-lg font-bold text-red-700">
                        Rp {{ number_format($transaction->remaining, 0, ',', '.') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ITEMS --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-200">

            <h2 class="font-semibold text-gray-900">
                Detail Barang
            </h2>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="text-left px-5 py-3 font-semibold text-gray-700">
                            Barang
                        </th>

                        <th class="text-left px-5 py-3 font-semibold text-gray-700">
                            Variasi
                        </th>

                        <th class="text-right px-5 py-3 font-semibold text-gray-700">
                            Harga
                        </th>

                        <th class="text-center px-5 py-3 font-semibold text-gray-700">
                            Qty
                        </th>

                        <th class="text-right px-5 py-3 font-semibold text-gray-700">
                            Subtotal
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100">

                    @foreach($transaction->items as $item)

                        <tr>

                            <td class="px-5 py-4">
                                {{ $item->product_name }}
                            </td>

                            <td class="px-5 py-4 text-gray-500">
                                {{ $item->variant_label ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                {{ $item->quantity }}
                            </td>

                            <td class="px-5 py-4 text-right font-medium">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

    {{-- PAYMENT --}}
    @if($transaction->status !== 'paid')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- FORM --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">

                <h2 class="font-semibold text-gray-900">
                    Bayar Tagihan
                </h2>

                <p class="text-sm text-gray-500 mt-1 mb-5">
                    Masukkan jumlah pembayaran customer.
                </p>

                <form
                    action="{{ route('pos.credit.payment', $transaction) }}"
                    method="POST"
                    class="space-y-5"
                >

                    @csrf

                    {{-- AMOUNT --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Pembayaran
                        </label>

                        <div class="relative">

                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">
                                Rp
                            </span>

                            <input
                                type="number"
                                name="amount"
                                min="1"
                                max="{{ $transaction->remaining }}"
                                value="{{ old('amount', $transaction->remaining) }}"
                                required
                                class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-3 focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            >

                        </div>

                        <p class="text-xs text-gray-500 mt-2">
                            Maksimal pembayaran:
                            <span class="font-medium">
                                Rp {{ number_format($transaction->remaining, 0, ',', '.') }}
                            </span>
                        </p>

                    </div>

                    {{-- PAYMENT METHOD --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Metode Pembayaran
                        </label>

                        <div class="grid grid-cols-2 gap-3">

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cash"
                                    class="peer hidden"
                                    {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }}
                                >

                                <div class="border border-gray-200 rounded-lg p-3 text-center peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900 transition">

                                    <i class="fa-solid fa-money-bill-wave mb-1"></i>

                                    <div class="text-sm font-medium">
                                        Cash
                                    </div>

                                </div>

                            </label>

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="qris"
                                    class="peer hidden"
                                    {{ old('payment_method') === 'qris' ? 'checked' : '' }}
                                >

                                <div class="border border-gray-200 rounded-lg p-3 text-center peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900 transition">

                                    <i class="fa-solid fa-qrcode mb-1"></i>

                                    <div class="text-sm font-medium">
                                        QRIS
                                    </div>

                                </div>

                            </label>

                        </div>

                    </div>

                    {{-- NOTE --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan
                        </label>

                        <textarea
                            name="note"
                            rows="3"
                            placeholder="Catatan pembayaran..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                        >{{ old('note') }}</textarea>

                    </div>

                    <button
                        type="submit"
                        class="w-full bg-gray-900 text-white rounded-lg py-3 font-medium hover:bg-gray-800 transition"
                    >
                        <i class="fa-solid fa-check mr-2"></i>
                        Simpan Pembayaran
                    </button>

                </form>

            </div>

            {{-- PAYMENT HISTORY --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5">

                <h2 class="font-semibold text-gray-900">
                    Riwayat Pembayaran
                </h2>

                <div class="mt-5 space-y-3">

                    @forelse($transaction->payments->sortByDesc('created_at') as $payment)

                        <div class="border border-gray-200 rounded-lg p-4">

                            <div class="flex items-start justify-between gap-3">

                                <div>

                                    <div class="font-semibold text-gray-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $payment->created_at->format('d M Y H:i') }}
                                    </div>

                                </div>

                                <div>

                                    @if($payment->payment_method === 'cash')

                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">
                                            Cash
                                        </span>

                                    @else

                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                                            QRIS
                                        </span>

                                    @endif

                                </div>

                            </div>

                            @if($payment->note)

                                <div class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">
                                    {{ $payment->note }}
                                </div>

                            @endif

                            @if($payment->user)

                                <div class="text-xs text-gray-400 mt-2">
                                    Diproses oleh {{ $payment->user->name }}
                                </div>

                            @endif

                        </div>

                    @empty

                        <div class="text-center py-10">

                            <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
                            </div>

                            <p class="text-sm text-gray-500">
                                Belum ada pembayaran.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    @else

        {{-- LUNAS --}}
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">

            <div class="w-14 h-14 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-3">

                <i class="fa-solid fa-check text-green-600 text-xl"></i>

            </div>

            <h2 class="text-lg font-bold text-green-800">
                Invoice Sudah Lunas
            </h2>

            <p class="text-sm text-green-700 mt-1">
                Tidak ada sisa tagihan untuk invoice ini.
            </p>

        </div>

    @endif

</div>

@endsection
