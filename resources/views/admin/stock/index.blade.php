@extends('layouts.app')

@section('title', 'Kelola Barang')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.dataTables.min.css">
@endpush

@section('content')

    <div class="p-4 max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-lg font-semibold">Riwayat Stok Masuk / Keluar</h1>
            <a href="{{ route('stock.create') }}" class="bg-blue-600 text-white text-sm px-3 py-2 rounded-lg">+ Catat Stok</a>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 text-green-700 border border-green-200 rounded p-3 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
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
                    @forelse ($movements as $m)
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
                    @empty
                        <tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada data stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $movements->links() }}</div>
    </div>
</body>
</html>
@endsection
