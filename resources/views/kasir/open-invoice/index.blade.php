@extends('layouts.app')

@section('title', 'Open Invoice')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Open Invoice
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Daftar transaksi yang ditunda dan belum dibayar.
            </p>
        </div>

        <a
            href="{{ route('pos.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                   bg-indigo-600 text-white rounded-lg text-sm font-medium
                   hover:bg-indigo-700 transition"
        >
            <svg
                class="w-5 h-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Buka Point of Sale
        </a>
    </div>


    {{-- CONTENT --}}
    @if($transactions->count())

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- TABLE HEADER --}}
            <div class="px-5 py-4 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <h2 class="font-semibold text-gray-900">
                        Invoice Tertunda
                    </h2>

                    <span class="text-sm text-gray-500">
                        {{ $transactions->count() }} Invoice
                    </span>

                </div>

            </div>


            {{-- DESKTOP TABLE --}}
            <div class="hidden md:block overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>

                            {{-- INVOICE --}}
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Invoice
                            </th>

                            {{-- TANGGAL --}}
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Tanggal
                            </th>

                            {{-- CUSTOMER --}}
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Customer
                            </th>

                            {{-- ITEM --}}
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Item
                            </th>

                            {{-- TOTAL --}}
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                Total
                            </th>

                            {{-- AKSI --}}
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @foreach($transactions as $transaction)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- INVOICE --}}
                                <td class="px-5 py-4">

                                    <div class="font-semibold text-gray-900">
                                        {{ $transaction->invoice_number }}
                                    </div>

                                    <div class="mt-1">

                                        <span
                                            class="inline-flex items-center px-2 py-0.5
                                                   rounded-full text-xs font-medium
                                                   bg-yellow-100 text-yellow-700"
                                        >
                                            Open
                                        </span>

                                    </div>

                                </td>


                                {{-- DATE --}}
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>


                                {{-- CUSTOMER --}}
                                <td class="px-5 py-4">

                                    @if($transaction->customer)

                                        <div class="font-medium text-gray-900">
                                            {{ $transaction->customer->name }}
                                        </div>

                                        @if($transaction->customer->phone)

                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $transaction->customer->phone }}
                                            </div>

                                        @endif

                                    @else

                                        <span class="text-sm text-gray-400">
                                            -
                                        </span>

                                    @endif

                                </td>


                                {{-- ITEM --}}
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $transaction->items->sum('quantity') }} item
                                </td>


                                {{-- TOTAL --}}
                                <td class="px-5 py-4 text-right">

                                    <span class="font-semibold text-gray-900">
                                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                    </span>

                                </td>


                                {{-- ACTION --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- BUKA --}}
                                        <a
                                            href="{{ route('pos.index', ['open_invoice' => $transaction->id]) }}"
                                            class="inline-flex items-center justify-center gap-1.5
                                                   px-3 py-2 rounded-lg
                                                   bg-indigo-600 text-white
                                                   text-sm font-medium
                                                   hover:bg-indigo-700 transition
                                                   whitespace-nowrap"
                                        >

                                            <svg
                                                class="w-4 h-4 shrink-0"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12H3m12 0-4-4m4 4-4 4M21 5v14"
                                                />
                                            </svg>

                                            Buka

                                        </a>


                                        {{-- EDIT --}}
                                        <a
                                            href="{{ route('pos.open-invoice.edit', $transaction) }}"
                                            class="inline-flex items-center justify-center gap-1.5
                                                   px-3 py-2 rounded-lg
                                                   border border-gray-200
                                                   bg-white
                                                   text-gray-700
                                                   text-sm font-medium
                                                   hover:bg-gray-50
                                                   hover:border-gray-300
                                                   transition
                                                   whitespace-nowrap"
                                        >

                                            <svg
                                                class="w-4 h-4 shrink-0"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                />
                                            </svg>

                                            Edit

                                        </a>


                                        {{-- HAPUS --}}
                                        <form
                                            method="POST"
                                            action="{{ route('pos.open-invoice.cancel', $transaction) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus Open Invoice ini?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center gap-1.5
                                                       px-3 py-2 rounded-lg
                                                       bg-red-50 text-red-600
                                                       text-sm font-medium
                                                       hover:bg-red-100 transition
                                                       whitespace-nowrap"
                                            >

                                                <svg
                                                    class="w-4 h-4 shrink-0"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6V11M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h12"
                                                    />
                                                </svg>

                                                Hapus

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- MOBILE CARD --}}
            <div class="md:hidden divide-y divide-gray-100">

                @foreach($transactions as $transaction)

                    <div class="p-4">

                        {{-- TOP --}}
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <div class="font-semibold text-gray-900 truncate">
                                    {{ $transaction->invoice_number }}
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </div>

                            </div>


                            <span
                                class="inline-flex items-center px-2 py-0.5
                                       rounded-full text-xs font-medium
                                       bg-yellow-100 text-yellow-700
                                       shrink-0"
                            >
                                Open
                            </span>

                        </div>


                        {{-- CUSTOMER --}}
                        @if($transaction->customer)

                            <div class="mt-4">

                                <div class="text-xs text-gray-500">
                                    Customer
                                </div>

                                <div class="font-semibold text-gray-900 mt-0.5">
                                    {{ $transaction->customer->name }}
                                </div>

                                @if($transaction->customer->phone)

                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $transaction->customer->phone }}
                                    </div>

                                @endif

                            </div>

                        @endif


                        {{-- INFO --}}
                        <div class="mt-4 grid grid-cols-2 gap-3">

                            {{-- ITEM --}}
                            <div>

                                <div class="text-xs text-gray-500">
                                    Item
                                </div>

                                <div class="font-medium text-gray-900">
                                    {{ $transaction->items->sum('quantity') }} item
                                </div>

                            </div>


                            {{-- TOTAL --}}
                            <div>

                                <div class="text-xs text-gray-500">
                                    Total
                                </div>

                                <div class="font-semibold text-gray-900">
                                    Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                </div>

                            </div>

                        </div>


                        {{-- ACTION MOBILE --}}
                        <div class="mt-4 flex items-stretch gap-2">

                            {{-- BUKA --}}
                            <a
                                href="{{ route('pos.index', ['open_invoice' => $transaction->id]) }}"
                                class="flex-1 min-w-0 inline-flex items-center justify-center gap-2
                                       px-3 py-2.5 rounded-lg
                                       bg-indigo-600 text-white
                                       text-sm font-medium
                                       hover:bg-indigo-700 transition"
                            >

                                <svg
                                    class="w-4 h-4 shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12H3m12 0-4-4m4 4-4 4M21 5v14"
                                    />
                                </svg>

                                <span class="truncate">
                                    Buka di Kasir
                                </span>

                            </a>


                            {{-- EDIT --}}
                            <a
                                href="{{ route('pos.open-invoice.edit', $transaction) }}"
                                class="flex-1 min-w-0 inline-flex items-center justify-center gap-2
                                       px-3 py-2.5 rounded-lg
                                       border border-gray-200
                                       bg-white
                                       text-gray-700
                                       text-sm font-medium
                                       hover:bg-gray-50
                                       hover:border-gray-300
                                       transition"
                            >

                                <svg
                                    class="w-4 h-4 shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>

                                <span>
                                    Edit
                                </span>

                            </a>


                            {{-- HAPUS --}}
                            <form
                                method="POST"
                                action="{{ route('pos.open-invoice.cancel', $transaction) }}"
                                onsubmit="return confirm('Yakin ingin menghapus Open Invoice ini?')"
                                class="shrink-0"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="h-full w-11 px-2.5
                                           inline-flex items-center justify-center
                                           rounded-lg
                                           bg-red-50 text-red-600
                                           hover:bg-red-100
                                           transition"
                                    title="Hapus"
                                >

                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6V11M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h12"
                                        />
                                    </svg>

                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @else

        {{-- EMPTY STATE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">

            <div class="px-6 py-16 text-center">

                <div
                    class="mx-auto w-16 h-16 rounded-full
                           bg-gray-100 flex items-center justify-center"
                >

                    <svg
                        class="w-8 h-8 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 14.25l6-6m-5.25-3h6.5A2.75 2.75 0 0119 8v8a2.75 2.75 0 01-2.75 2.75h-8.5A2.75 2.75 0 015 16V8a2.75 2.75 0 012.75-2.75h1.5"
                        />

                    </svg>

                </div>

                <h3 class="mt-4 text-lg font-semibold text-gray-900">
                    Belum ada Open Invoice
                </h3>

                <p class="mt-1 text-sm text-gray-500">
                    Simpan transaksi dari Point of Sale sebagai Open Invoice
                    untuk muncul di sini.
                </p>

                <a
                    href="{{ route('pos.index') }}"
                    class="inline-flex items-center gap-2 mt-5
                           px-4 py-2.5 rounded-lg
                           bg-indigo-600 text-white
                           text-sm font-medium
                           hover:bg-indigo-700 transition"
                >
                    Buka Kasir
                </a>

            </div>

        </div>

    @endif

</div>
@endsection
