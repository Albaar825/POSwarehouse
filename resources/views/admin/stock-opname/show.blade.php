@extends('layouts.app')

@section('title', 'Detail Stock Opname')

@section('content')

<div class="flex justify-between items-center mb-4">

    <div>

        <h1 class="text-lg font-semibold">
            Detail Stock Opname
        </h1>

        <p class="text-sm text-gray-500">
            {{ $stockOpname->opname_date->format('d F Y') }}
        </p>

    </div>


    <a href="{{ route('admin.stock-opname.index') }}"
        class="px-4 py-2 text-sm border border-gray-300 rounded-lg">

        Kembali

    </a>

</div>


{{-- Informasi --}}
<div class="bg-white rounded-lg shadow p-4 mb-4">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div>

            <p class="text-xs text-gray-500">
                Tanggal
            </p>

            <p class="font-medium">
                {{ $stockOpname->opname_date->format('d/m/Y') }}
            </p>

        </div>


        <div>

            <p class="text-xs text-gray-500">
                Petugas
            </p>

            <p class="font-medium">
                {{ $stockOpname->user->name ?? '-' }}
            </p>

        </div>


        <div>

            <p class="text-xs text-gray-500">
                Status
            </p>

            <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                Selesai
            </span>

        </div>

    </div>


    @if ($stockOpname->note)

        <div class="mt-4 pt-4 border-t">

            <p class="text-xs text-gray-500">
                Catatan
            </p>

            <p class="text-sm">
                {{ $stockOpname->note }}
            </p>

        </div>

    @endif

</div>


{{-- Detail --}}
<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="p-4 border-b">

        <h2 class="font-semibold">
            Hasil Pemeriksaan
        </h2>

    </div>


    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead class="bg-gray-50">

                <tr>

                    <th class="p-3 text-left">
                        SKU
                    </th>

                    <th class="p-3 text-left">
                        Barang
                    </th>

                    <th class="p-3 text-center">
                        Stok Sistem
                    </th>

                    <th class="p-3 text-center">
                        Stok Fisik
                    </th>

                    <th class="p-3 text-center">
                        Selisih
                    </th>

                    <th class="p-3 text-left">
                        Catatan
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach ($stockOpname->details as $detail)

                    <tr class="border-t">

                        <td class="p-3">
                            {{ $detail->product->sku }}
                        </td>

                        <td class="p-3">
                            {{ $detail->product->name }}
                        </td>

                        <td class="p-3 text-center">
                            {{ $detail->system_stock }}
                        </td>

                        <td class="p-3 text-center">
                            {{ $detail->physical_stock }}
                        </td>

                        <td class="p-3 text-center">

                            @if ($detail->difference > 0)

                                <span class="font-semibold text-green-600">
                                    +{{ $detail->difference }}
                                </span>

                            @elseif ($detail->difference < 0)

                                <span class="font-semibold text-red-600">
                                    {{ $detail->difference }}
                                </span>

                            @else

                                <span class="text-gray-500">
                                    0
                                </span>

                            @endif

                        </td>

                        <td class="p-3">
                            {{ $detail->note ?? '-' }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
