@extends('layouts.app')

@section('title', 'Stock Opname')

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
        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #d97706;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.2);
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
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #fef3c7 !important; /* amber-100 */
            color: #92400e !important;
            border-color: #d97706 !important;
        }
    </style>
@endpush

@section('content')

<div class="flex justify-between items-center mb-4">

    <div>
        <h1 class="text-lg font-semibold">
            Stock Opname
        </h1>

        <p class="text-sm text-gray-500">
            Riwayat pengecekan stok fisik warehouse
        </p>
    </div>

    <a href="{{ route('admin.stock-opname.create') }}"
        class="bg-amber-600 hover:bg-amber-700 text-white text-sm px-4 py-2 rounded-lg">

        + Stock Opname

    </a>

</div>


<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="overflow-x-auto p-2">

        <table id="opname-table" class="w-full text-sm">

            <thead class="bg-gray-50 text-gray-600">

                <tr>

                    <th class="p-3 text-left">
                        #
                    </th>

                    <th class="p-3 text-left">
                        Tanggal
                    </th>

                    <th class="p-3 text-left">
                        Petugas
                    </th>

                    <th class="p-3 text-left">
                        Jumlah Barang
                    </th>

                    <th class="p-3 text-left">
                        Status
                    </th>

                    <th class="p-3 text-left">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach ($opnames as $opname)

                    <tr class="border-t hover:bg-gray-50">

                        <td class="p-3">
                            {{-- diisi otomatis oleh DataTables lewat columns.render --}}
                        </td>

                        <td class="p-3">
                            {{ $opname->opname_date->format('d/m/Y') }}
                        </td>

                        <td class="p-3">
                            {{ $opname->user->name ?? '-' }}
                        </td>

                        <td class="p-3">
                            {{ $opname->details_count ?? $opname->details->count() }}
                            barang
                        </td>

                        <td class="p-3">

                            @if ($opname->status === 'completed')

                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Selesai
                                </span>

                            @else

                                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">
                                    Draft
                                </span>

                            @endif

                        </td>

                        <td class="p-3">

                            <a href="{{ route('admin.stock-opname.show', $opname) }}"
                                class="text-amber-600 hover:text-amber-700 hover:underline font-medium">

                                Detail

                            </a>

                        </td>

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
            $('#opname-table').DataTable({
                order: [[1, 'desc']],
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        targets: 5,
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
                emptyTable: "Belum ada data stock opname."
            });
        });
    </script>
@endpush
