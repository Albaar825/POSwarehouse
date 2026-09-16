@extends('layouts.app')

@section('title', 'Invoice Kredit')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.375rem 0.65rem;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #111827;
            box-shadow: 0 0 0 1px #111827;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35rem 0.65rem !important;
            margin-left: 2px;
            border-radius: 0.5rem !important;
            font-size: 0.85rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #111827 !important;
            color: white !important;
            border-color: #111827 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
            border-color: #d1d5db !important;
        }
    </style>
@endpush

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

        
           <a href="{{ route('pos.index') }}"
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

        <div class="overflow-x-auto p-2">

            <table id="credit-table" class="w-full text-sm">

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

                    @foreach($transactions as $transaction)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- INVOICE --}}
                            <td class="px-5 py-4" data-order="{{ $transaction->created_at->timestamp }}">

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
                            <td class="px-5 py-4 font-medium text-gray-900" data-order="{{ $transaction->total }}">
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </td>

                            {{-- PAID --}}
                            <td class="px-5 py-4 text-green-600 font-medium" data-order="{{ $transaction->paid }}">
                                Rp {{ number_format($transaction->paid, 0, ',', '.') }}
                            </td>

                            {{-- REMAINING --}}
                            <td class="px-5 py-4 text-red-600 font-semibold" data-order="{{ $transaction->remaining }}">
                                Rp {{ number_format($transaction->remaining, 0, ',', '.') }}
                            </td>

                            {{-- DUE DATE --}}
                            <td class="px-5 py-4" data-order="{{ $transaction->due_date?->timestamp ?? 0 }}">

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

                                
                                    href="{{ route('pos.credit.show', $transaction) }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white text-xs font-medium hover:bg-gray-800 transition"
                                >
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    Detail / Bayar
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#credit-table').DataTable({
                order: [[0, 'desc']], // urutkan berdasarkan invoice terbaru
                columnDefs: [
                    {
                        targets: 7, // kolom Aksi
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                emptyTable: "Tidak ada invoice kredit. Semua invoice kredit sudah lunas."
            });
        });
    </script>
@endpush