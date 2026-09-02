@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <div class="p-4 max-w-2xl mx-auto">

        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('stock.index') }}" class="text-gray-400 hover:text-amber-600 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <h1 class="text-lg font-semibold text-gray-800">Catat Stok Masuk / Keluar</h1>
        </div>
        <p class="text-sm text-gray-500 mb-6 ml-7">Perbarui jumlah stok barang di warehouse</p>

        <form method="POST" action="{{ route('stock.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf

            {{-- Barang --}}
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1.5">Barang</label>
                <div class="relative">
                    <select
                        id="product_id"
                        name="product_id"
                        onchange="updateProductInfo(this)"
                        class="w-full appearance-none border rounded-lg pl-3 pr-9 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $errors->has('product_id') ? 'border-red-300' : 'border-gray-300' }}"
                        required
                    >
                        <option value="">- Pilih barang -</option>
                        @foreach ($products as $p)
                            <option
                                value="{{ $p->id }}"
                                data-stock="{{ $p->stock }}"
                                data-unit="{{ $p->unit }}"
                                {{ old('product_id') == $p->id ? 'selected' : '' }}
                            >
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                <p id="stock-info" class="mt-1.5 text-xs text-gray-500 {{ old('product_id') ? '' : 'hidden' }}">
                    Stok saat ini: <span id="stock-value" class="font-medium text-gray-700">0</span> <span id="stock-unit"></span>
                </p>
                @error('product_id')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="in" class="peer sr-only" {{ old('type', 'in') === 'in' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 border-2 border-gray-200 rounded-lg px-4 py-3 transition peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-gray-300">
                            <span class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 shrink-0">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v9.586l3.293-3.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 111.414-1.414L9 13.586V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-medium text-gray-800">Stok Masuk</span>
                                <span class="block text-xs text-gray-500">Restock barang</span>
                            </span>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="out" class="peer sr-only" {{ old('type') === 'out' ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 border-2 border-gray-200 rounded-lg px-4 py-3 transition peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-gray-300">
                            <span class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600 shrink-0">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 17a1 1 0 01-1-1V6.414L5.707 9.707a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0l5 5a1 1 0 01-1.414 1.414L11 6.414V16a1 1 0 01-1 1z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-sm font-medium text-gray-800">Stok Keluar</span>
                                <span class="block text-xs text-gray-500">Pengeluaran manual</span>
                            </span>
                        </div>
                    </label>
                </div>
                @error('type')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jumlah --}}
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah</label>
                <div class="relative">
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        min="1"
                        value="{{ old('quantity') }}"
                        class="w-full border rounded-lg px-3 py-2.5 pr-16 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $errors->has('quantity') ? 'border-red-300' : 'border-gray-300' }}"
                        required
                    >
                    <span id="quantity-unit" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></span>
                </div>
                @error('quantity')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Catatan --}}
            <div>
                <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea
                    id="note"
                    name="note"
                    rows="3"
                    placeholder="Contoh: retur dari pelanggan, restock dari supplier A, dll."
                    class="w-full border rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 {{ $errors->has('note') ? 'border-red-300' : 'border-gray-300' }}"
                >{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium py-2.5 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('stock.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        function updateProductInfo(select) {
            const opt = select.options[select.selectedIndex];
            const info = document.getElementById('stock-info');
            const stockValue = document.getElementById('stock-value');
            const stockUnit = document.getElementById('stock-unit');
            const quantityUnit = document.getElementById('quantity-unit');

            if (opt && opt.value) {
                stockValue.textContent = opt.dataset.stock ?? '0';
                stockUnit.textContent = opt.dataset.unit ?? '';
                quantityUnit.textContent = opt.dataset.unit ?? '';
                info.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
                quantityUnit.textContent = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateProductInfo(document.getElementById('product_id'));
        });
    </script>
@endsection
