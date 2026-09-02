@extends('layouts.app')

@section('title', 'Stock Opname')

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
        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg">

        + Stock Opname

    </a>

</div>


<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

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

                @forelse ($opnames as $opname)

                    <tr class="border-t hover:bg-gray-50">

                        <td class="p-3">
                            {{ $opnames->firstItem() + $loop->index }}
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

                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                    Draft
                                </span>

                            @endif

                        </td>

                        <td class="p-3">

                            <a href="{{ route('admin.stock-opname.show', $opname) }}"
                                class="text-blue-600 hover:underline">

                                Detail

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="p-6 text-center text-gray-500">

                            Belum ada data stock opname.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


<div class="mt-4">
    {{ $opnames->links() }}
</div>

@endsection
