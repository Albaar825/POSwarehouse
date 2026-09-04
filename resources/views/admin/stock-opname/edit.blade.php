@extends('layouts.app')

@section('title', 'Edit Stock Opname')

@section('content')

<div class="max-w-6xl mx-auto p-4">

    {{-- HEADER --}}
    <div class="flex items-center gap-2 mb-1">

        <a href="{{ route('admin.stock-opname.show', $stockOpname) }}"
            class="text-gray-400 hover:text-amber-600 shrink-0">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor">

                <path fill-rule="evenodd"
                    d="M12.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L8.414 10l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />

            </svg>

        </a>

        <h1 class="text-lg font-semibold text-gray-800">
            Edit Stock Opname
        </h1>

    </div>

    <p class="text-sm text-gray-500 mb-6 ml-7">
        Periksa kembali hasil opname sebelum disimpan. Pastikan stok fisik sesuai dengan hasil pengecekan warehouse.
    </p>


    {{-- ERROR --}}
    @if ($errors->any())

        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-sm">

            <p class="font-medium mb-1">
                Terdapat kesalahan:
            </p>

            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        action="{{ route('admin.stock-opname.update', $stockOpname) }}"
        method="POST"
        id="opname-form">

        @csrf
        @method('PUT')


        {{-- INFORMASI OPNAME --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">

            <h2 class="font-semibold text-gray-800 mb-4">
                Informasi Opname
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- TANGGAL --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Opname
                    </label>

                    <input
                        type="date"
                        name="opname_date"
                        value="{{ old('opname_date', $stockOpname->opname_date->format('Y-m-d')) }}"
                        required
                        class="w-full border rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-amber-500
                        focus:border-amber-500 border-gray-300">

                    @error('opname_date')
                        <p class="text-xs text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- CATATAN --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Catatan
                        <span class="text-gray-400 font-normal">
                            (opsional)
                        </span>
                    </label>

                    <input
                        type="text"
                        name="note"
                        value="{{ old('note', $stockOpname->note) }}"
                        placeholder="Contoh: pengecekan stok bulanan"
                        class="w-full border rounded-lg px-3 py-2.5 text-sm
                        focus:outline-none focus:ring-2 focus:ring-amber-500
                        focus:border-amber-500 border-gray-300">

                    @error('note')
                        <p class="text-xs text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>


        {{-- PEMERIKSAAN STOK --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- HEADER --}}
            <div class="p-6 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>

                        <h2 class="font-semibold text-gray-800">
                            Pemeriksaan Stok
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Pilih produk untuk melihat dan mengubah stok fisik setiap varian.
                        </p>

                    </div>

                    <div class="text-sm text-gray-600">

                        Selisih ditemukan:

                        <span
                            id="diff-count"
                            class="font-semibold text-gray-800">

                            0

                        </span>

                    </div>

                </div>


                {{-- SEARCH --}}
                <div class="relative mt-4">

                    <svg
                        class="absolute left-3 top-3 w-5 h-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.05 6.05a7.5 7.5 0 0 0 10.6 10.6Z" />

                    </svg>

                    <input
                        type="text"
                        id="product-search"
                        placeholder="Cari nama produk atau SKU..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                        focus:outline-none focus:ring-2 focus:ring-amber-500
                        focus:border-amber-500">

                </div>

            </div>


            {{-- PRODUCT LIST --}}
            <div id="product-list" class="divide-y divide-gray-100">

                @forelse ($products as $product)

                    @php
                        $productDetails = $stockOpname->details
                            ->whereIn('product_variant_id', $product->variants->pluck('id'));

                        $hasDifference = $productDetails->contains(function ($detail) {
                            return $detail->difference != 0;
                        });
                    @endphp


                    <div
                        class="product-item"
                        data-product-name="{{ strtolower($product->name) }}"
                        data-product-sku="{{ strtolower($product->sku) }}">

                        {{-- PRODUCT HEADER --}}
                        <button
                            type="button"
                            class="product-toggle w-full p-4 text-left hover:bg-gray-50 transition">

                            <div class="flex items-center justify-between gap-4">

                                <div class="flex items-center gap-3 min-w-0">

                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">

                                        <svg
                                            class="w-5 h-5 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />

                                        </svg>

                                    </div>

                                    <div class="min-w-0">

                                        <p class="font-semibold text-sm text-gray-800 truncate">
                                            {{ $product->name }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-0.5">
                                            SKU: {{ $product->sku }}
                                            ·
                                            {{ $product->variants->count() }} varian
                                        </p>

                                    </div>

                                </div>


                                <div class="flex items-center gap-3 shrink-0">

                                    @if ($hasDifference)

                                        <span class="hidden sm:inline-flex px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">
                                            Ada selisih
                                        </span>

                                    @endif

                                    <svg
                                        class="product-chevron w-5 h-5 text-gray-400 transition-transform"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="m6 9 6 6 6-6" />

                                    </svg>

                                </div>

                            </div>

                        </button>


                        {{-- VARIANTS --}}
                        <div class="variant-container hidden bg-gray-50 border-t border-gray-100">

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm">

                                    <thead class="bg-gray-100 text-gray-600">

                                        <tr>

                                            <th class="p-3 text-left font-medium">
                                                SKU Varian
                                            </th>

                                            <th class="p-3 text-left font-medium">
                                                Warna / Size
                                            </th>

                                            <th class="p-3 text-center font-medium">
                                                Stok Sistem
                                            </th>

                                            <th class="p-3 text-center font-medium">
                                                Stok Fisik
                                            </th>

                                            <th class="p-3 text-center font-medium">
                                                Selisih
                                            </th>

                                            <th class="p-3 text-left font-medium">
                                                Catatan
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody class="divide-y divide-gray-200">

                                        @foreach ($product->variants as $variant)

                                            @php
                                                $detail = $productDetails->firstWhere(
                                                    'product_variant_id',
                                                    $variant->id
                                                );

                                                $physicalStock = $detail
                                                    ? $detail->physical_stock
                                                    : $variant->stock;

                                                $detailNote = $detail
                                                    ? $detail->note
                                                    : '';
                                            @endphp


                                            <tr
                                                class="variant-row hover:bg-white"
                                                data-row>

                                                <td class="p-3">

                                                    <input
                                                        type="hidden"
                                                        name="variants[{{ $variant->id }}][product_variant_id]"
                                                        value="{{ $variant->id }}">

                                                    <span class="text-gray-600">
                                                        {{ $variant->sku_variant }}
                                                    </span>

                                                </td>


                                                <td class="p-3">

                                                    <div class="font-medium text-gray-700">
                                                        {{ $variant->label() }}
                                                    </div>

                                                </td>


                                                <td class="p-3 text-center">

                                                    <span
                                                        class="system-stock text-gray-700"
                                                        data-system="{{ $variant->stock }}">

                                                        {{ $variant->stock }}

                                                    </span>

                                                </td>


                                                <td class="p-3">

                                                    <input
                                                        type="number"
                                                        name="variants[{{ $variant->id }}][physical_stock]"
                                                        value="{{ old(
                                                            'variants.' . $variant->id . '.physical_stock',
                                                            $physicalStock
                                                        ) }}"
                                                        min="0"
                                                        required
                                                        class="physical-stock w-24 mx-auto block border border-gray-300 rounded-lg px-2 py-2 text-center text-sm
                                                        focus:outline-none focus:ring-2 focus:ring-amber-500
                                                        focus:border-amber-500"
                                                        data-system="{{ $variant->stock }}">

                                                </td>


                                                <td class="p-3 text-center">

                                                    <span
                                                        class="difference inline-flex items-center justify-center min-w-[2.5rem] px-2 py-1 rounded-full text-xs font-medium">

                                                        0

                                                    </span>

                                                </td>


                                                <td class="p-3">

                                                    <input
                                                        type="text"
                                                        name="variants[{{ $variant->id }}][note]"
                                                        value="{{ old(
                                                            'variants.' . $variant->id . '.note',
                                                            $detailNote
                                                        ) }}"
                                                        placeholder="Catatan"
                                                        class="w-full min-w-[150px] border border-gray-300 rounded-lg px-2 py-2 text-sm
                                                        focus:outline-none focus:ring-2 focus:ring-amber-500
                                                        focus:border-amber-500">

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center text-gray-400 text-sm">
                        Tidak ada produk untuk diperiksa.
                    </div>

                @endforelse

            </div>


            {{-- NO SEARCH RESULT --}}
            <div
                id="no-search-result"
                class="hidden p-10 text-center text-gray-400 text-sm">

                Produk tidak ditemukan.

            </div>


            {{-- FOOTER --}}
            <div class="p-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">

                <a
                    href="{{ route('admin.stock-opname.show', $stockOpname) }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 border border-gray-200 rounded-lg transition text-center">

                    Batal

                </a>


                <button
                    type="button"
                    id="submit-button"
                    class="px-5 py-2.5 text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">

                    Simpan Perubahan

                </button>

            </div>

        </div>

    </form>

</div>


{{-- CONFIRMATION MODAL --}}
<div
    id="confirm-modal"
    class="hidden fixed inset-0 z-50 items-center justify-center p-4">

    <div
        class="absolute inset-0 bg-black/50"
        id="modal-backdrop">
    </div>


    <div class="relative bg-white rounded-2xl w-full max-w-md shadow-xl">

        <div class="p-5 border-b border-gray-100">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">

                    <svg
                        class="w-5 h-5 text-amber-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86l-7.36 12.73A2 2 0 004.66 19.5h14.68a2 2 0 001.73-2.91L13.71 3.86a2 2 0 00-3.42 0Z" />

                    </svg>

                </div>

                <div>

                    <h3 class="font-semibold text-gray-800">
                        Konfirmasi Perubahan
                    </h3>

                    <p class="text-xs text-gray-500">
                        Pastikan data sudah benar
                    </p>

                </div>

            </div>

        </div>


        <div class="p-5">

            <p class="text-sm text-gray-600">
                Apakah kamu yakin ingin menyimpan perubahan stock opname?
            </p>

            <div class="mt-3 bg-amber-50 border border-amber-100 rounded-lg p-3">

                <p class="text-xs text-amber-800">
                    Perubahan stok fisik akan memengaruhi penyesuaian stok varian.
                    Pastikan jumlah yang dimasukkan sudah sesuai hasil pengecekan fisik.
                </p>

            </div>

        </div>


        <div class="p-4 border-t border-gray-100 flex justify-end gap-3">

            <button
                type="button"
                id="cancel-modal"
                class="px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">

                Batal

            </button>


            <button
                type="button"
                id="confirm-submit"
                class="px-4 py-2.5 text-sm font-medium bg-amber-600 hover:bg-amber-700 text-white rounded-lg">

                Ya, Simpan

            </button>

        </div>

    </div>

</div>


@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | HITUNG SELISIH
    |--------------------------------------------------------------------------
    */

    function updateDifference(input) {

        const systemStock =
            parseInt(input.dataset.system) || 0;

        const physicalStock =
            parseInt(input.value) || 0;

        const difference =
            physicalStock - systemStock;

        const row =
            input.closest('[data-row]');

        if (!row) {
            return;
        }

        const badge =
            row.querySelector('.difference');

        if (!badge) {
            return;
        }

        badge.textContent =
            difference > 0
                ? '+' + difference
                : difference;


        badge.classList.remove(
            'bg-green-100',
            'text-green-700',
            'bg-red-100',
            'text-red-700',
            'bg-gray-100',
            'text-gray-500'
        );


        if (difference > 0) {

            badge.classList.add(
                'bg-green-100',
                'text-green-700'
            );

        } else if (difference < 0) {

            badge.classList.add(
                'bg-red-100',
                'text-red-700'
            );

        } else {

            badge.classList.add(
                'bg-gray-100',
                'text-gray-500'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL DIFFERENCE
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.physical-stock')
        .forEach(function (input) {

            updateDifference(input);

            input.addEventListener('input', function () {

                updateDifference(this);

                refreshDiffCount();

            });

        });


    /*
    |--------------------------------------------------------------------------
    | JUMLAH SELISIH
    |--------------------------------------------------------------------------
    */

    function refreshDiffCount() {

        let total = 0;

        document
            .querySelectorAll('.physical-stock')
            .forEach(function (input) {

                const systemStock =
                    parseInt(input.dataset.system) || 0;

                const physicalStock =
                    parseInt(input.value) || 0;

                if (physicalStock !== systemStock) {
                    total++;
                }

            });


        const counter =
            document.getElementById('diff-count');

        if (counter) {
            counter.textContent = total;
        }

    }


    refreshDiffCount();


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ACCORDION
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.product-toggle')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const container =
                    this.parentElement.querySelector('.variant-container');

                const chevron =
                    this.querySelector('.product-chevron');

                if (!container) {
                    return;
                }


                container.classList.toggle('hidden');

                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | SEARCH PRODUK
    |--------------------------------------------------------------------------
    */

    const searchInput =
        document.getElementById('product-search');

    const productItems =
        document.querySelectorAll('.product-item');

    const noResult =
        document.getElementById('no-search-result');


    if (searchInput) {

        searchInput.addEventListener('input', function () {

            const keyword =
                this.value.toLowerCase().trim();

            let visibleCount = 0;


            productItems.forEach(function (item) {

                const name =
                    item.dataset.productName || '';

                const sku =
                    item.dataset.productSku || '';


                const match =
                    !keyword ||
                    name.includes(keyword) ||
                    sku.includes(keyword);


                if (match) {

                    item.classList.remove('hidden');

                    visibleCount++;

                } else {

                    item.classList.add('hidden');

                }

            });


            if (noResult) {

                if (visibleCount === 0) {
                    noResult.classList.remove('hidden');
                } else {
                    noResult.classList.add('hidden');
                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION MODAL
    |--------------------------------------------------------------------------
    */

    const submitButton =
        document.getElementById('submit-button');

    const modal =
        document.getElementById('confirm-modal');

    const cancelModal =
        document.getElementById('cancel-modal');

    const confirmSubmit =
        document.getElementById('confirm-submit');

    const backdrop =
        document.getElementById('modal-backdrop');


    function openModal() {

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    }


    function closeModal() {

        modal.classList.add('hidden');

        modal.classList.remove('flex');

    }


    if (submitButton) {

        submitButton.addEventListener('click', function () {

            refreshDiffCount();

            openModal();

        });

    }


    if (cancelModal) {

        cancelModal.addEventListener('click', function () {

            closeModal();

        });

    }


    if (backdrop) {

        backdrop.addEventListener('click', function () {

            closeModal();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    if (confirmSubmit) {

        confirmSubmit.addEventListener('click', function () {

            const form =
                document.getElementById('opname-form');

            if (!form) {
                return;
            }


            confirmSubmit.disabled = true;

            confirmSubmit.textContent =
                'Menyimpan...';


            form.submit();

        });

    }

});

</script>

@endpush
