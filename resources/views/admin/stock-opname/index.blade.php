@extends('layouts.app')

@section('title', 'Stock Opname')

@push('head')
    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.dataTables.min.css"
    >

    <style>
        .dataTables_wrapper {
            padding: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.4rem 0.65rem;
            font-size: 0.875rem;
            outline: none;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #d97706;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.15);
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_info {
            color: #6b7280;
            font-size: 0.875rem;
            padding-top: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 0.75rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.35rem 0.7rem !important;
            margin-left: 2px;
            border-radius: 0.5rem !important;
            font-size: 0.85rem;
            border: 1px solid transparent !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #d97700 !important;
            color: white !important;
            border-color: #d97700 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #fef3c7 !important;
            color: #92400e !important;
            border-color: #d97700 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: transparent !important;
            color: #9ca3af !important;
            border-color: transparent !important;
        }

        table.dataTable thead th {
            border-bottom: 1px solid #f3f4f6 !important;
        }

        table.dataTable.no-footer {
            border-bottom: none !important;
        }

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        #opname-table tbody:empty::after {
            content: "Belum ada data stock opname.";
            display: table-cell;
            text-align: center;
            padding: 2.5rem;
            color: #9ca3af;
            font-size: 0.875rem;
        }
    </style>
@endpush


@section('content')

<div class="max-w-6xl mx-auto p-4">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

        <div>

            <h1 class="text-lg font-semibold text-gray-800">
                Stock Opname
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Riwayat pengecekan stok fisik warehouse
            </p>

        </div>


        {{-- BUTTON CREATE --}}
        <a
            href="{{ route('admin.stock-opname.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd"
                />
            </svg>

            Stock Opname

        </a>

    </div>


    {{-- TABLE CARD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- CARD HEADER --}}
        <div class="p-6 border-b border-gray-100">

            <div class="flex items-center justify-between gap-3 flex-wrap">

                <div>

                    <h2 class="font-semibold text-gray-800">
                        Riwayat Stock Opname
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Daftar pemeriksaan dan penyesuaian stok fisik warehouse.
                    </p>

                </div>


                <div class="text-xs text-gray-500">

                    Total:

                    <span class="font-medium text-gray-800">
                        {{ $opnames->count() }}
                    </span>

                    data

                </div>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="overflow-x-auto">

            <table
                id="opname-table"
                class="w-full text-sm"
            >

                {{-- TABLE HEADER --}}
                <thead class="bg-gray-50 text-gray-600">

                    <tr>

                        <th class="p-3 text-left font-medium">
                            #
                        </th>

                        <th class="p-3 text-left font-medium">
                            Tanggal
                        </th>

                        <th class="p-3 text-left font-medium">
                            Petugas
                        </th>

                        <th class="p-3 text-left font-medium">
                            Jumlah Barang
                        </th>

                        <th class="p-3 text-left font-medium">
                            Status
                        </th>

                        <th class="p-3 text-left font-medium">
                            Aksi
                        </th>

                    </tr>

                </thead>


                {{-- TABLE BODY --}}
                <tbody>

                    @foreach ($opnames as $opname)

                        <tr class="border-t border-gray-100 hover:bg-gray-50 transition">

                            {{-- NO --}}
                            <td class="p-3">
                                {{-- Nomor diisi oleh DataTables --}}
                            </td>


                            {{-- TANGGAL --}}
                            <td class="p-3 whitespace-nowrap">

                                <div class="font-medium text-gray-800">
                                    {{ $opname->opname_date->format('d/m/Y') }}
                                </div>

                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $opname->opname_date->format('d M Y') }}
                                </div>

                            </td>


                            {{-- PETUGAS --}}
                            <td class="p-3">

                                <div class="font-medium text-gray-800">
                                    {{ $opname->user->name ?? '-' }}
                                </div>

                            </td>


                            {{-- JUMLAH BARANG --}}
                            <td class="p-3">

                                <span class="text-gray-700">
                                    {{ $opname->details_count ?? $opname->details->count() }}
                                </span>

                                <span class="text-gray-500">
                                    barang
                                </span>

                            </td>


                            {{-- STATUS --}}
                            <td class="p-3">

                                @if ($opname->status === 'completed')

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">

                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                        Selesai

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700">

                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>

                                        Draft

                                    </span>

                                @endif

                            </td>


                            {{-- AKSI --}}
                            <td class="p-3">

                                <div class="flex items-center gap-3">

                                    {{-- DETAIL --}}
                                    <a
                                        href="{{ route('admin.stock-opname.show', $opname) }}"
                                        class="inline-flex items-center gap-1.5 text-amber-600 hover:text-amber-700 font-medium hover:underline"
                                    >

                                        Detail

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-4 h-4"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>

                                    </a>


                                    {{-- EDIT HANYA UNTUK DRAFT --}}
                                    @if ($opname->status !== 'completed')

                                        <a
                                            href="{{ route('admin.stock-opname.edit', $opname) }}"
                                            class="inline-flex items-center gap-1.5 text-gray-600 hover:text-gray-800 font-medium hover:underline"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    d="M17.414 2.586a2 2 0 010 2.828l-9.9 9.9a1 1 0 01-.39.242l-4 1.333a1 1 0 01-1.265-1.265l1.333-4a1 1 0 01.242-.39l9.9-9.9a2 2 0 012.828 0z"
                                                />

                                                <path
                                                    d="M12.586 4.586l2.828 2.828"
                                                />
                                            </svg>

                                            Edit

                                        </a>

                                    @endif

                                </div>

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

        $('#opname-table').DataTable({

            /*
            |--------------------------------------------------------------------------
            | SORTING
            |--------------------------------------------------------------------------
            */

            order: [
                [1, 'desc']
            ],


            /*
            |--------------------------------------------------------------------------
            | PAGINATION
            |--------------------------------------------------------------------------
            */

            pageLength: 10,

            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'Semua']
            ],


            /*
            |--------------------------------------------------------------------------
            | COLUMN SETTINGS
            |--------------------------------------------------------------------------
            */

            columnDefs: [

                /*
                |--------------------------------------------------------------------------
                | NOMOR
                |--------------------------------------------------------------------------
                */

                {
                    targets: 0,

                    orderable: false,

                    searchable: false,

                    render: function (
                        data,
                        type,
                        row,
                        meta
                    ) {

                        return (
                            meta.row +
                            meta.settings._iDisplayStart +
                            1
                        );

                    }
                },


                /*
                |--------------------------------------------------------------------------
                | AKSI
                |--------------------------------------------------------------------------
                */

                {
                    targets: 5,

                    orderable: false,

                    searchable: false
                }

            ],


            /*
            |--------------------------------------------------------------------------
            | LANGUAGE
            |--------------------------------------------------------------------------
            */

            language: {

                search: "Cari:",

                lengthMenu: "Tampilkan _MENU_ data",

                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",

                infoEmpty: "Tidak ada data",

                infoFiltered: "(disaring dari _MAX_ total data)",

                zeroRecords: "Data tidak ditemukan",

                emptyTable: "Belum ada data stock opname.",

                paginate: {

                    first: "Awal",

                    last: "Akhir",

                    next: "Berikutnya",

                    previous: "Sebelumnya"

                }

            }

        });

    });

</script>

@endpush
