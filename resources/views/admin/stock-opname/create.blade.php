@extends('layouts.app')

@section('title', 'Stock Opname Baru')

@section('content')

<div class="max-w-6xl mx-auto p-4">

    <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('admin.stock-opname.index') }}" class="text-gray-400 hover:text-amber-600 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
        </a>
        <h1 class="text-lg font-semibold text-gray-800">Stock Opname Baru</h1>
    </div>
    <p class="text-sm text-gray-500 mb-6 ml-7">
        Masukkan jumlah stok fisik berdasarkan hasil pengecekan warehouse.
    </p>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-sm">
            Periksa kembali isian yang bertanda merah di bawah.
        </div>
    @endif

    <form action="{{ route('admin.stock-opname.store') }}" method="POST">

        @csrf

        {{-- Informasi Opname --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">

            <h2 class="font-semibold text-gray-800 mb-4">Informasi Opname</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Opname</label>
                    <input
                        type="date"
                        name="opname_date"
                        value="{{ old('opname_date', now()->format('Y-m-d')) }}"
                        required
                        class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $errors->has('opname_date') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('opname_date')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input
                        type="text"
                        name="note"
                        value="{{ old('note') }}"
                        placeholder="Contoh: pengecekan stok bulanan"
                        class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $errors->has('note') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('note')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

        </div>

        {{-- Daftar Barang --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="font-semibold text-gray-800">Pemeriksaan Stok</h2>
                    <p class="text-xs text-gray-500 mt-1">Isi jumlah stok fisik yang benar-benar tersedia.</p>
                </div>
                <div class="text-sm text-gray-600">
                    Total barang: <span class="font-medium text-gray-800">{{ count($products) }}</span>
                    &nbsp;·&nbsp;
                    Selisih ditemukan: <span id="diff-count" class="font-medium text-gray-800">0</span>
                </div>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-3 text-left font-medium">SKU</th>
                            <th class="p-3 text-left font-medium">Barang</th>
                            <th class="p-3 text-center font-medium">Stok Sistem</th>
                            <th class="p-3 text-center font-medium">Stok Fisik</th>
                            <th class="p-3 text-center font-medium">Selisih</th>
                            <th class="p-3 text-left font-medium">Catatan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($products as $index => $product)

                            <tr class="hover:bg-gray-50" data-row>

                                <td class="p-3 text-gray-500">
                                    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $product->id }}">
                                    {{ $product->sku }}
                                </td>

                                <td class="p-3">
                                    <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $product->unit }}</div>
                                </td>

                                <td class="p-3 text-center">
                                    <span class="system-stock text-gray-700">{{ $product->stock }}</span>
                                </td>

                                <td class="p-3">
                                    <input
                                        type="number"
                                        name="products[{{ $index }}][physical_stock]"
                                        value="{{ old("products.$index.physical_stock", $product->stock) }}"
                                        min="0"
                                        required
                                        class="physical-stock w-24 mx-auto block border border-gray-300 rounded-lg px-2 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                        data-system="{{ $product->stock }}">
                                </td>

                                <td class="p-3 text-center">
                                    <span class="difference inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        0
                                    </span>
                                </td>

                                <td class="p-3">
                                    <input
                                        type="text"
                                        name="products[{{ $index }}][note]"
                                        value="{{ old("products.$index.note") }}"
                                        placeholder="Catatan"
                                        class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-400 text-sm">
                                    Tidak ada barang untuk diperiksa.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="p-4 border-t border-gray-100 flex justify-end gap-3">

                <a href="{{ route('admin.stock-opname.index') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-lg transition">
                    Batal
                </a>

                <button
                    type="submit"
                    onclick="return confirm('Simpan hasil stock opname? Stok barang akan disesuaikan dengan stok fisik.')"
                    class="px-5 py-2.5 text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                    Simpan Stock Opname
                </button>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>
    function refreshDiffCount() {
        const total = document.querySelectorAll('[data-row] .difference.text-red-700, [data-row] .difference.text-green-700').length;
        const counter = document.getElementById('diff-count');
        if (counter) counter.textContent = total;
    }

    document.querySelectorAll('.physical-stock').forEach(function (input) {

        input.addEventListener('input', function () {

            const systemStock = parseInt(this.dataset.system) || 0;
            const physicalStock = parseInt(this.value) || 0;
            const difference = physicalStock - systemStock;

            const row = this.closest('tr');
            const badge = row.querySelector('.difference');

            badge.textContent = difference > 0 ? '+' + difference : difference;

            badge.classList.remove(
                'bg-green-100', 'text-green-700',
                'bg-red-100', 'text-red-700',
                'bg-gray-100', 'text-gray-500'
            );

            if (difference > 0) {
                badge.classList.add('bg-green-100', 'text-green-700');
            } else if (difference < 0) {
                badge.classList.add('bg-red-100', 'text-red-700');
            } else {
                badge.classList.add('bg-gray-100', 'text-gray-500');
            }

            refreshDiffCount();
        });
    });

    refreshDiffCount();
</script>
@endpush

@endsection
