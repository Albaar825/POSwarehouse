@extends('layouts.app')

@section('title', 'Stock Opname Baru')

@section('content')

<div class="mb-4">

    <h1 class="text-lg font-semibold">
        Stock Opname Baru
    </h1>

    <p class="text-sm text-gray-500">
        Masukkan jumlah stok fisik berdasarkan hasil pengecekan warehouse.
    </p>

</div>


<form action="{{ route('admin.stock-opname.store') }}"
    method="POST">

    @csrf


    {{-- Informasi Opname --}}
    <div class="bg-white rounded-lg shadow p-4 mb-4">

        <h2 class="font-semibold mb-4">
            Informasi Opname
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Opname
                </label>

                <input
                    type="date"
                    name="opname_date"
                    value="{{ old('opname_date', now()->format('Y-m-d')) }}"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">

            </div>


            <div>

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Catatan
                </label>

                <input
                    type="text"
                    name="note"
                    value="{{ old('note') }}"
                    placeholder="Contoh: pengecekan stok bulanan"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">

            </div>

        </div>

    </div>


    {{-- Daftar Barang --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">

        <div class="p-4 border-b">

            <h2 class="font-semibold">
                Pemeriksaan Stok
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Isi jumlah stok fisik yang benar-benar tersedia.
            </p>

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

                    @foreach ($products as $index => $product)

                        <tr class="border-t">

                            <input
                                type="hidden"
                                name="products[{{ $index }}][product_id]"
                                value="{{ $product->id }}">


                            <td class="p-3">
                                {{ $product->sku }}
                            </td>


                            <td class="p-3">

                                <div class="font-medium">
                                    {{ $product->name }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $product->unit }}
                                </div>

                            </td>


                            <td class="p-3 text-center">

                                <span class="system-stock">
                                    {{ $product->stock }}
                                </span>

                            </td>


                            <td class="p-3">

                                <input
                                    type="number"
                                    name="products[{{ $index }}][physical_stock]"
                                    value="{{ old("products.$index.physical_stock", $product->stock) }}"
                                    min="0"
                                    required
                                    class="physical-stock w-24 mx-auto block border border-gray-300 rounded-lg px-2 py-2 text-center"
                                    data-system="{{ $product->stock }}">

                            </td>


                            <td class="p-3 text-center">

                                <span class="difference text-gray-500">
                                    0
                                </span>

                            </td>


                            <td class="p-3">

                                <input
                                    type="text"
                                    name="products[{{ $index }}][note]"
                                    value="{{ old("products.$index.note") }}"
                                    placeholder="Catatan"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm">

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="p-4 border-t flex justify-end gap-2">

            <a href="{{ route('admin.stock-opname.index') }}"
                class="px-4 py-2 text-sm border border-gray-300 rounded-lg">

                Batal

            </a>

            <button
                type="submit"
                onclick="return confirm('Simpan hasil stock opname? Stok barang akan disesuaikan dengan stok fisik.')"
                class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">

                Simpan Stock Opname

            </button>

        </div>

    </div>

</form>


@push('scripts')

<script>

document.querySelectorAll('.physical-stock').forEach(function(input) {

    input.addEventListener('input', function() {

        const systemStock = parseInt(this.dataset.system) || 0;

        const physicalStock = parseInt(this.value) || 0;

        const difference = physicalStock - systemStock;

        const row = this.closest('tr');

        const differenceElement = row.querySelector('.difference');

        differenceElement.textContent =
            difference > 0
                ? '+' + difference
                : difference;

        differenceElement.classList.remove(
            'text-green-600',
            'text-red-600',
            'text-gray-500'
        );

        if (difference > 0) {

            differenceElement.classList.add('text-green-600');

        } else if (difference < 0) {

            differenceElement.classList.add('text-red-600');

        } else {

            differenceElement.classList.add('text-gray-500');

        }

    });

});

</script>

@endpush

@endsection
