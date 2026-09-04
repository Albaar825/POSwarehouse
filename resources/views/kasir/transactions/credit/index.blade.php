@extends('layouts.app')

@section('title', 'Invoice Kredit')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Invoice Kredit
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Kelola invoice customer yang masih memiliki tagihan.
            </p>
        </div>

        <a
            href="{{ route('pos.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition"
        >
            <i class="fa-solid fa-cash-register"></i>
            Kembali ke POS
        </a>

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

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Invoice
                        </th>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Customer
                        </th>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Total
                        </th>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Terbayar
                        </th>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Sisa
                        </th>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Jatuh Tempo
                        </th>

                        <th class="text-left px-5 py-4 font-semibold text-gray-700">
                            Status
                        </th>

                        <th class="text-right px-5 py-4 font-semibold text-gray-700">
                            Aksi
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($transactions as $transaction)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- INVOICE --}}
                            <td class="px-5 py-4">

                                <div class="font-semibold text-gray-900">
                                    {{ $transaction->invoice_number }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $transaction->created_at->format('d M Y H:i') }}
                                </div>

                            </td>

                            {{-- CUSTOMER --}}
                            <td class="px-5 py-4">

                                <div class="font-medium text-gray-900">
                                    {{ $transaction->customer->name ?? '-' }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $transaction->customer->phone ?? '-' }}
                                </div>

                            </td>

                            {{-- TOTAL --}}
                            <td class="px-5 py-4 font-medium text-gray-900">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </td>

                            {{-- PAID --}}
                            <td class="px-5 py-4 text-green-600 font-medium">
                                Rp {{ number_format($transaction->paid, 0, ',', '.') }}
                            </td>

                            {{-- REMAINING --}}
                            <td class="px-5 py-4 text-red-600 font-semibold">
                                Rp {{ number_format($transaction->remaining, 0, ',', '.') }}
                            </td>

                            {{-- DUE DATE --}}
                            <td class="px-5 py-4">

                                @if($transaction->due_date)

                                    <div class="
                                        {{ $transaction->due_date->isPast()
                                            ? 'text-red-600 font-semibold'
                                            : 'text-gray-700'
                                        }}
                                    ">
                                        {{ $transaction->due_date->format('d M Y') }}
                                    </div>

                                    @if($transaction->due_date->isPast())
                                        <div class="text-xs text-red-500 mt-1">
                                            Jatuh tempo
                                        </div>
                                    @endif

                                @else
                                    -
                                @endif

                            </td>

                            {{-- STATUS --}}
                            <td class="px-5 py-4">

                                @if($transaction->status === 'on_hold')

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        On Hold
                                    </span>

                                @elseif($transaction->status === 'partial')

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        Partial
                                    </span>

                                @else

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ ucfirst($transaction->status) }}
                                    </span>

                                @endif

                            </td>

                            {{-- ACTION --}}
                            <td class="px-5 py-4 text-right">

                                <a
                                    href="{{ route('pos.credit.show', $transaction) }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition"
                                >
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    Detail / Bayar
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="px-5 py-12 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-file-invoice-dollar text-gray-400 text-xl"></i>
                                    </div>

                                    <h3 class="font-semibold text-gray-900">
                                        Tidak ada invoice kredit
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Semua invoice kredit sudah lunas.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($transactions->hasPages())

            <div class="px-5 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
