@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <div class="p-4 max-w-md mx-auto">
        <h1 class="text-lg font-semibold mb-4">Catat Stok Masuk / Keluar</h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded p-3 text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('stock.store') }}" class="bg-white rounded-lg shadow p-4 space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Barang</label>
                <select name="product_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">- Pilih barang -</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (stok: {{ $p->stock }} {{ $p->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tipe</label>
                <select name="type" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="in">Stok Masuk (restock)</option>
                    <option value="out">Stok Keluar (manual)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah</label>
                <input type="number" name="quantity" min="1" class="w-full border rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Catatan (opsional)</label>
                <textarea name="note" class="w-full border rounded-lg px-3 py-2" rows="2"></textarea>
            </div>
            <button class="w-full bg-blue-600 text-white py-2 rounded-lg">Simpan</button>
        </form>
    </div>
</body>
</html>
@endsection
