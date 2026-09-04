<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * ============================================================
     * HALAMAN POS
     * ============================================================
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD PRODUCT
        |--------------------------------------------------------------------------
        |
        | Semua variant di-load, termasuk variant yang stoknya 0.
        | Ini penting supaya gambar variant tetap tersedia untuk gallery.
        |
        */

        $products = Product::with([
            'category',

            'variants' => function ($query) {
                $query
                    ->with([
                        'images' => function ($imageQuery) {
                            $imageQuery
                                ->orderByDesc('is_primary')
                                ->orderBy('sort_order')
                                ->orderBy('id');
                        }
                    ])
                    ->orderBy('color')
                    ->orderBy('size');
            }
        ])
            ->where('is_active', true)

            /*
            |--------------------------------------------------------------------------
            | PRODUK HANYA DITAMPILKAN JIKA ADA STOK
            |--------------------------------------------------------------------------
            */

            ->whereHas('variants', function ($query) {
                $query->where('stock', '>', 0);
            })

            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DATA PRODUCT UNTUK JAVASCRIPT
        |--------------------------------------------------------------------------
        */

        $productsJson = $products->map(function ($product) {

            /*
            |--------------------------------------------------------------------------
            | SEMUA VARIANT
            |--------------------------------------------------------------------------
            */

            $allVariants = $product->variants;

            /*
            |--------------------------------------------------------------------------
            | VARIANT YANG MASIH BISA DIJUAL
            |--------------------------------------------------------------------------
            */

            $availableVariants = $allVariants
                ->filter(function ($variant) {
                    return (int) $variant->stock > 0;
                })
                ->values();

            /*
            |--------------------------------------------------------------------------
            | GAMBAR BERDASARKAN WARNA
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | Hitam:
            | M  -> gambar 1, gambar 2
            | L  -> gambar 1, gambar 2
            | XL -> gambar 1, gambar 2
            |
            | Hasil:
            |
            | hitam => [
            |     gambar 1,
            |     gambar 2
            | ]
            |
            */

            $colorImages = $allVariants
                ->groupBy(function ($variant) {
                    return strtolower(
                        trim($variant->color ?? '')
                    );
                })
                ->map(function ($variants) {

                    return $variants
                        ->flatMap(function ($variant) {

                            return $variant->images
                                ->map(function ($image) {
                                    return asset(
                                        'storage/' . $image->image
                                    );
                                });
                        })
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();
                });

            /*
            |--------------------------------------------------------------------------
            | CARI GAMBAR UTAMA PRODUK
            |--------------------------------------------------------------------------
            |
            | Karena gambar sekarang berada di
            | product_variant_images, ambil primary image
            | dari variant pertama yang memilikinya.
            |
            */

            $productImage = null;

            foreach ($allVariants as $variant) {

                /*
                | Cari primary image
                */

                $primaryImage = $variant->images
                    ->first(function ($image) {
                        return $image->is_primary;
                    });

                if ($primaryImage) {

                    $productImage = asset(
                        'storage/' . $primaryImage->image
                    );

                    break;
                }

                /*
                | Jika tidak ada primary,
                | ambil gambar pertama.
                */

                if (
                    !$productImage &&
                    $variant->images->isNotEmpty()
                ) {

                    $firstImage = $variant->images->first();

                    $productImage = asset(
                        'storage/' . $firstImage->image
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | DATA VARIANT UNTUK JAVASCRIPT
            |--------------------------------------------------------------------------
            */

            $variants = $availableVariants
                ->map(function ($variant) use ($colorImages) {

                    $colorKey = strtolower(
                        trim($variant->color ?? '')
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | GAMBAR VARIANT
                    |--------------------------------------------------------------------------
                    */

                    $images = $variant->images
                        ->map(function ($image) {
                            return asset(
                                'storage/' . $image->image
                            );
                        })
                        ->filter()
                        ->unique()
                        ->values()
                        ->toArray();

                    /*
                    |--------------------------------------------------------------------------
                    | FALLBACK GAMBAR WARNA
                    |--------------------------------------------------------------------------
                    |
                    | Jika variant size tertentu tidak memiliki
                    | gambar sendiri, gunakan gambar berdasarkan warna.
                    |
                    */

                    if (empty($images)) {

                        $images = $colorImages
                            ->get($colorKey, []);
                    }

                    return [
                        'id' => $variant->id,

                        'color' => $variant->color,

                        'size' => $variant->size,

                        'sku_variant' => $variant->sku_variant,

                        'label' => $variant->label(),

                        'stock' => (int) $variant->stock,

                        'images' => $images,
                    ];
                })
                ->values();

            /*
            |--------------------------------------------------------------------------
            | WARNA YANG MASIH TERSEDIA
            |--------------------------------------------------------------------------
            */

            $colors = $availableVariants
                ->pluck('color')
                ->filter(function ($color) {
                    return filled($color);
                })
                ->unique()
                ->values()
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | RETURN PRODUCT
            |--------------------------------------------------------------------------
            */

            return [
                'id' => $product->id,

                'name' => $product->name,

                'sku' => $product->sku,

                'price' => (int) $product->price,

                /*
                |--------------------------------------------------------------------------
                | GAMBAR PRODUCT CARD
                |--------------------------------------------------------------------------
                */

                'image' => $productImage,

                'category' => $product->category->name
                    ?? 'Tanpa Kategori',

                'colors' => $colors,

                /*
                |--------------------------------------------------------------------------
                | GAMBAR PER WARNA
                |--------------------------------------------------------------------------
                */

                'color_images' => $colorImages->toArray(),

                /*
                |--------------------------------------------------------------------------
                | VARIANT
                |--------------------------------------------------------------------------
                */

                'variants' => $variants,
            ];
        })->values();

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        $customers = Customer::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'kasir.pos.index',
            compact(
                'products',
                'productsJson',
                'customers'
            )
        );
    }

    /**
     * ============================================================
     * SIMPAN TRANSAKSI
     * ============================================================
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI REQUEST
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'items.*.product_variant_id' => [
                'required',
                'exists:product_variants,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'payment_method' => [
                'required',
                'in:cash,qris,credit',
            ],

            'paid' => [
                'required',
                'integer',
                'min:0',
            ],

            'customer_id' => [
                'nullable',
                'exists:customers,id',
            ],

            'customer_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'customer_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDASI KHUSUS KREDIT
        |--------------------------------------------------------------------------
        */

        if ($data['payment_method'] === 'credit') {

            if (empty($data['customer_name'])) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'customer_name' =>
                            'Nama customer wajib diisi untuk transaksi kredit.',
                    ]);
            }

            if (empty($data['customer_phone'])) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'customer_phone' =>
                            'Nomor HP customer wajib diisi untuk transaksi kredit.',
                    ]);
            }

            if (empty($data['due_date'])) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'due_date' =>
                            'Tanggal jatuh tempo wajib diisi untuk transaksi kredit.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PROSES TRANSAKSI
        |--------------------------------------------------------------------------
        */

        $transaction = DB::transaction(function () use ($data) {

            $total = 0;

            $itemsData = [];

            /*
            |--------------------------------------------------------------------------
            | CEK SEMUA ITEM DAN STOK
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                /*
                |--------------------------------------------------------------------------
                | LOCK PRODUCT
                |--------------------------------------------------------------------------
                */

                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);

                /*
                |--------------------------------------------------------------------------
                | LOCK VARIANT
                |--------------------------------------------------------------------------
                */

                $variant = ProductVariant::lockForUpdate()
                    ->where('id', $item['product_variant_id'])
                    ->where('product_id', $product->id)
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | CEK STOK
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $variant->stock <
                    (int) $item['quantity']
                ) {

                    throw ValidationException::withMessages([
                        'items' =>
                            'Stok ' .
                            $product->name .
                            ' - ' .
                            $variant->label() .
                            ' tidak cukup. ' .
                            'Stok tersedia: ' .
                            $variant->stock,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | HITUNG SUBTOTAL
                |--------------------------------------------------------------------------
                */

                $subtotal =
                    $product->price *
                    $item['quantity'];

                $total += $subtotal;

                /*
                |--------------------------------------------------------------------------
                | SIMPAN DATA ITEM SEMENTARA
                |--------------------------------------------------------------------------
                */

                $itemsData[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | TENTUKAN PEMBAYARAN
            |--------------------------------------------------------------------------
            */

            $customerId = null;

            if ($data['payment_method'] === 'credit') {

                /*
                |--------------------------------------------------------------------------
                | CARI / BUAT CUSTOMER
                |--------------------------------------------------------------------------
                */

                $customer = Customer::where(
                    'phone',
                    $data['customer_phone']
                )->first();

                if (!$customer) {

                    $customer = Customer::create([
                        'name' => $data['customer_name'],
                        'phone' => $data['customer_phone'],
                    ]);

                } else {

                    $customer->update([
                        'name' => $data['customer_name'],
                    ]);
                }

                $customerId = $customer->id;

                /*
                |--------------------------------------------------------------------------
                | CREDIT
                |--------------------------------------------------------------------------
                */

                $paid = 0;

                $change = 0;

                $status = 'on_hold';

            } else {

                /*
                |--------------------------------------------------------------------------
                | CASH / QRIS
                |--------------------------------------------------------------------------
                */

                $paid = $data['paid'];

                /*
                |--------------------------------------------------------------------------
                | CEK UANG BAYAR
                |--------------------------------------------------------------------------
                */

                if ($paid < $total) {

                    throw ValidationException::withMessages([
                        'paid' =>
                            'Uang bayar kurang dari total belanja.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | HITUNG KEMBALIAN
                |--------------------------------------------------------------------------
                */

                $change = $paid - $total;

                $status = 'paid';
            }

            /*
            |--------------------------------------------------------------------------
            | GENERATE NOMOR INVOICE
            |--------------------------------------------------------------------------
            */

            $invoiceNumber =
                'INV-' .
                now()->format('Ymd') .
                '-' .
                Str::upper(
                    Str::random(5)
                );

            /*
            |--------------------------------------------------------------------------
            | BUAT TRANSAKSI
            |--------------------------------------------------------------------------
            */

            $transaction = Transaction::create([

                'invoice_number' =>
                    $invoiceNumber,

                'user_id' =>
                    Auth::id(),

                'customer_id' =>
                    $data['payment_method'] === 'credit'
                        ? $customerId
                        : null,

                'total' =>
                    $total,

                'paid' =>
                    $paid,

                'change' =>
                    $change,

                'payment_method' =>
                    $data['payment_method'],

                'status' =>
                    $status,

                'due_date' =>
                    $data['payment_method'] === 'credit'
                        ? $data['due_date']
                        : null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | SIMPAN ITEM TRANSAKSI
            |--------------------------------------------------------------------------
            */

            foreach ($itemsData as $entry) {

                $product = $entry['product'];

                $variant = $entry['variant'];

                /*
                |--------------------------------------------------------------------------
                | TRANSACTION ITEM
                |--------------------------------------------------------------------------
                */

                TransactionItem::create([

                    'transaction_id' =>
                        $transaction->id,

                    'product_id' =>
                        $product->id,

                    'product_variant_id' =>
                        $variant->id,

                    'product_name' =>
                        $product->name,

                    'variant_label' =>
                        $variant->label(),

                    'price' =>
                        $product->price,

                    'quantity' =>
                        $entry['quantity'],

                    'subtotal' =>
                        $entry['subtotal'],
                ]);

                /*
                |--------------------------------------------------------------------------
                | STOCK MOVEMENT
                |--------------------------------------------------------------------------
                */

                StockMovement::create([

                    'product_id' =>
                        $product->id,

                    'product_variant_id' =>
                        $variant->id,

                    'user_id' =>
                        Auth::id(),

                    'type' =>
                        'out',

                    'quantity' =>
                        $entry['quantity'],

                    'source' =>
                        'transaction',

                    'transaction_id' =>
                        $transaction->id,

                    'note' =>
                        'Penjualan POS ' .
                        $transaction->invoice_number,
                ]);

                /*
                |--------------------------------------------------------------------------
                | KURANGI STOCK VARIANT
                |--------------------------------------------------------------------------
                */

                $variant->decrement(
                    'stock',
                    $entry['quantity']
                );

                /*
                |--------------------------------------------------------------------------
                | HITUNG TOTAL STOCK PRODUCT
                |--------------------------------------------------------------------------
                */

                $newTotalStock =
                    ProductVariant::where(
                        'product_id',
                        $product->id
                    )->sum('stock');

                /*
                |--------------------------------------------------------------------------
                | UPDATE STOCK PRODUCT
                |--------------------------------------------------------------------------
                */

                $product->update([
                    'stock' =>
                        $newTotalStock,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | CATAT PEMBAYARAN CASH / QRIS
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $data['payment_method'],
                    ['cash', 'qris']
                )
            ) {

                TransactionPayment::create([

                    'transaction_id' =>
                        $transaction->id,

                    'user_id' =>
                        Auth::id(),

                    'amount' =>
                        $total,

                    'payment_method' =>
                        $data['payment_method'],

                    'note' =>
                        'Pembayaran transaksi POS',
                ]);
            }

            return $transaction;
        });

        /*
        |--------------------------------------------------------------------------
        | REDIRECT KE RECEIPT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'pos.receipt',
                $transaction
            )
            ->with(
                'success',
                'Transaksi berhasil.'
            );
    }

    /**
     * ============================================================
     * STRUK / RECEIPT
     * ============================================================
     */
    public function receipt(Transaction $transaction)
    {
        $transaction->load([
            'items.product',
            'items.variant',
            'user',
            'customer',
        ]);

        return view(
            'kasir.pos.receipt',
            compact('transaction')
        );
    }

    /**
     * ============================================================
     * RIWAYAT TRANSAKSI
     * ============================================================
     */
    public function history()
    {
        $transactions = Transaction::with([
            'user',
            'customer',
        ])
            ->latest()
            ->paginate(15);

        return view(
            'kasir.transactions.index',
            compact('transactions')
        );
    }

    /**
     * ============================================================
     * DAFTAR TRANSAKSI KREDIT
     * ============================================================
     */
    public function creditIndex()
    {
        $transactions = Transaction::with([
            'customer',
            'user',
        ])
            ->where(
                'payment_method',
                'credit'
            )
            ->whereIn(
                'status',
                [
                    'on_hold',
                    'partial',
                ]
            )
            ->latest()
            ->paginate(15);

        return view(
            'kasir.transactions.credit.index',
            compact('transactions')
        );
    }

    /**
     * ============================================================
     * DETAIL TRANSAKSI KREDIT
     * ============================================================
     */
    public function creditShow(
        Transaction $transaction
    ) {
        if (
            $transaction->payment_method !== 'credit'
        ) {
            abort(404);
        }

        $transaction->load([
            'customer',
            'user',
            'items.product',
            'items.variant',
            'payments.user',
        ]);

        return view(
            'kasir.transactions.credit.show',
            compact('transaction')
        );
    }

    /**
     * ============================================================
     * PEMBAYARAN KREDIT
     * ============================================================
     */
    public function creditPayment(
        Request $request,
        Transaction $transaction
    ) {
        $data = $request->validate([

            'amount' => [
                'required',
                'integer',
                'min:1',
            ],

            'payment_method' => [
                'required',
                'in:cash,qris',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use (
            $data,
            $transaction
        ) {

            $transaction = Transaction::lockForUpdate()
                ->findOrFail($transaction->id);

            /*
            |--------------------------------------------------------------------------
            | CEK TRANSAKSI
            |--------------------------------------------------------------------------
            */

            if (
                $transaction->payment_method !== 'credit'
            ) {

                throw ValidationException::withMessages([
                    'transaction' =>
                        'Transaksi ini bukan transaksi kredit.',
                ]);
            }

            if (
                $transaction->status === 'paid'
            ) {

                throw ValidationException::withMessages([
                    'amount' =>
                        'Invoice ini sudah lunas.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | HITUNG SISA TAGIHAN
            |--------------------------------------------------------------------------
            */

            $remaining = max(
                0,
                $transaction->total -
                $transaction->paid
            );

            /*
            |--------------------------------------------------------------------------
            | CEK JUMLAH BAYAR
            |--------------------------------------------------------------------------
            */

            if (
                $data['amount'] >
                $remaining
            ) {

                throw ValidationException::withMessages([
                    'amount' =>
                        'Jumlah pembayaran melebihi sisa tagihan. ' .
                        'Sisa tagihan: Rp ' .
                        number_format(
                            $remaining,
                            0,
                            ',',
                            '.'
                        ),
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | SIMPAN PEMBAYARAN
            |--------------------------------------------------------------------------
            */

            TransactionPayment::create([

                'transaction_id' =>
                    $transaction->id,

                'user_id' =>
                    Auth::id(),

                'amount' =>
                    $data['amount'],

                'payment_method' =>
                    $data['payment_method'],

                'note' =>
                    $data['note'] ??
                    'Pembayaran cicilan invoice kredit',
            ]);

            /*
            |--------------------------------------------------------------------------
            | UPDATE TOTAL BAYAR
            |--------------------------------------------------------------------------
            */

            $newPaid =
                $transaction->paid +
                $data['amount'];

            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS
            |--------------------------------------------------------------------------
            */

            $newStatus =
                $newPaid >= $transaction->total
                    ? 'paid'
                    : 'partial';

            /*
            |--------------------------------------------------------------------------
            | UPDATE TRANSAKSI
            |--------------------------------------------------------------------------
            */

            $transaction->update([

                'paid' =>
                    $newPaid,

                'change' =>
                    0,

                'status' =>
                    $newStatus,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'pos.credit.show',
                $transaction
            )
            ->with(
                'success',
                'Pembayaran berhasil disimpan.'
            );
    }
}
