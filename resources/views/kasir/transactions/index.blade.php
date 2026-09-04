@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')

<div class="space-y-5">

    <div>

        <h2 class="text-xl font-bold text-gray-900">
            Riwayat Transaksi
        </h2>

        <p class="text-sm text-gray-500">
            Daftar transaksi yang dilakukan kasir
        </p>

    </div>


    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">

                    <tr>

                        <th class="text-left px-5 py-3 font-semibold text-gray-600">
                            Invoice
                        </th>

                        <th class="text-left px-5 py-3 font-semibold text-gray-600">
                            Tanggal
                        </th>

                        <th class="text-left px-5 py-3 font-semibold text-gray-600">
                            Kasir
                        </th>

                        <th class="text-left px-5 py-3 font-semibold text-gray-600">
                            Pembayaran
                        </th>

                        <th class="text-right px-5 py-3 font-semibold text-gray-600">
                            Total
                        </th>

                        <th class="text-right px-5 py-3 font-semibold text-gray-600">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse ($transactions as $transaction)

                        <tr class="hover:bg-gray-50">

                            <td class="px-5 py-4 font-semibold text-gray-900">
                                {{ $transaction->invoice_number }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ $transaction->user->name }}
                            </td>

                            <td class="px-5 py-4">

                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $transaction->payment_method === 'cash'
                                        ? 'bg-green-50 text-green-700'
                                        : 'bg-blue-50 text-blue-700' }}">
                                    {{ strtoupper($transaction->payment_method) }}
                                </span>

                            </td>

                            <td class="px-5 py-4 text-right font-semibold">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-4 text-right">

                                <a
                                    href="{{ route('pos.receipt', $transaction) }}"
                                    class="text-gray-700 hover:text-black font-medium"
                                >
                                    Lihat Invoice
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-5 py-12 text-center text-gray-500"
                            >
                                Belum ada transaksi.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($transactions->hasPages())

            <div class="p-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
