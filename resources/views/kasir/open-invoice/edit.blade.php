@extends('layouts.app')

@section('title', 'Edit Customer - Open Invoice')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex items-center gap-3 mb-6">


            <a href="{{ route('pos.open-invoice.index') }}"
            class="text-gray-400 hover:text-gray-700 transition"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <div>
            <h1 class="text-xl font-bold text-gray-900">
                Edit Data Customer
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $transaction->invoice_number }}
            </p>
        </div>

    </div>

    {{-- INFO --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
            </svg>
            <div class="text-sm text-blue-800">
                Halaman ini cuma buat ubah data customer (nama, no. HP, alamat). Barang dan total tagihan tidak bisa diubah di sini — kalau mau tambah barang, buka invoice-nya lewat menu Point of Sale.
            </div>
        </div>
    </div>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">

        <form method="POST" action="{{ route('pos.open-invoice.update', $transaction) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Customer <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="customer_name"
                    value="{{ old('customer_name', $transaction->customer->name ?? '') }}"
                    required
                    placeholder="Nama customer"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    No. HP
                </label>
                <input
                    type="text"
                    name="customer_phone"
                    value="{{ old('customer_phone', $transaction->customer->phone ?? '') }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Alamat
                </label>
                <textarea
                    name="customer_address"
                    rows="3"
                    placeholder="Alamat customer"
                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition resize-none"
                >{{ old('customer_address', $transaction->customer->address ?? '') }}</textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">

                    <a href="{{ route('pos.open-invoice.index') }}"
                    class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition"
                >
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
