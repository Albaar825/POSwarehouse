@extends('layouts.app')

@section('title', 'Kelola Barang')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.6rem !important;
            margin-left: 2px;
            border-radius: 0.375rem !important;
            font-size: 0.85rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #d97706 !important; /* amber-600 */
            color: white !important;
            border-color: #d97706 !important;
        }
    </style>
@endpush

@section('content')

    <div class="p-4 max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-lg font-semibold">Riwayat Stok Masuk / Keluar</h1>
            <a href="{{ route('stock.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-3 py-2 rounded-lg">+ Catat Stok</a>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 text-green-700 border border-green-200 rounded p-3 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-x-auto p-2">
            <table id="stock-table" class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 text-left">
                    <tr>
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Barang</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3">Jumlah</th>
                        <th class="p-3">Sumber</th>
                        <th class="p-3">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movements as $m)
                        <tr class="border-t">
                            <td class="p-3">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $m->product->name ?? '-' }}</td>
                            <td class="p-3">
                                @if ($m->type === 'in')
                                    <span class="text-green-600 font-medium">Masuk</span>
                                @else
                                    <span class="text-red-600 font-medium">Keluar</span>
                                @endif
                            </td>
                            <td class="p-3">{{ $m->quantity }}</td>
                            <td class="p-3 text-gray-500">{{ $m->source }}</td>
                            <td class="p-3">{{ $m->user->name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#stock-table').DataTable({
                order: [[0, 'desc']],
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
                emptyTable: "Belum ada data stok."
            });
        });
    </script>
@endpush
