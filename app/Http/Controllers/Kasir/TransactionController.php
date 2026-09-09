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
    public function create(Request $request)
    {
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
                    ->orderBy('id');
            }
        ])
            ->where('is_active', true)
            ->whereHas('variants', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orderBy('name')
            ->get();

        $productsJson = $products->map(function ($product) {

            $allVariants = $product->variants;

            /*
            |--------------------------------------------------------------------------
            | VARIANT GROUPS
            |--------------------------------------------------------------------------
            */

            $variantGroups = is_array($product->variant_groups)
                ? $product->variant_groups
                : [];

            $normalizedGroups = collect($variantGroups)
                ->values()
                ->map(function ($group, $groupIndex) {

                    $groupName = trim(
                        (string) (
                            $group['name']
                            ?? ('Variant ' . ($groupIndex + 1))
                        )
                    );

                    $values = collect(
                        $group['values'] ?? []
                    )
                        ->map(function ($value) {

                            if (is_array($value)) {

                                $name = trim(
                                    (string) (
                                        $value['name'] ?? ''
                                    )
                                );

                                $image = $value['image']
                                    ?? null;

                            } else {

                                $name = trim(
                                    (string) $value
                                );

                                $image = null;
                            }

                            if (!filled($name)) {
                                return null;
                            }

                            return [
                                'name' => $name,
                                'image' => $this->normalizeImageUrl(
                                    $image
                                ),
                            ];
                        })
                        ->filter()
                        ->values()
                        ->toArray();

                    return [
                        'name' => $groupName,

                        'type' => $group['type']
                            ?? (
                                $groupIndex === 0
                                    ? 'parent'
                                    : 'child'
                            ),

                        'values' => $values,
                    ];
                })
                ->filter(function ($group) {
                    return !empty($group['values']);
                })
                ->values()
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | FALLBACK VARIANT LAMA
            |--------------------------------------------------------------------------
            */

            if (empty($normalizedGroups)) {

                $attributeCount = $allVariants
                    ->map(function ($variant) {

                        return is_array(
                            $variant->attributes
                        )
                            ? count($variant->attributes)
                            : 0;
                    })
                    ->max() ?? 0;

                for (
                    $i = 0;
                    $i < $attributeCount;
                    $i++
                ) {

                    $values = $allVariants
                        ->map(function ($variant) use ($i) {

                            return is_array(
                                $variant->attributes
                            )
                                ? (
                                    $variant->attributes[$i]
                                    ?? null
                                )
                                : null;
                        })
                        ->filter(function ($value) {
                            return filled($value);
                        })
                        ->unique()
                        ->values()
                        ->map(function ($value) {

                            return [
                                'name' => $value,
                                'image' => null,
                            ];
                        })
                        ->toArray();

                    $normalizedGroups[] = [
                        'name' =>
                            'Variant ' . ($i + 1),

                        'type' =>
                            $i === 0
                                ? 'parent'
                                : 'child',

                        'values' =>
                            $values,
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | VARIANT IMAGE
            |--------------------------------------------------------------------------
            */

            $variantImages = [];

            foreach ($allVariants as $variant) {

                $images = $variant->images
                    ->map(function ($image) {

                        return $this->normalizeImageUrl(
                            $image->image
                        );

                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $variantImages[$variant->id] = $images;
            }

            /*
            |--------------------------------------------------------------------------
            | IMAGES BY VALUE
            |--------------------------------------------------------------------------
            */

            $imagesByValue = [];

            foreach (
                $normalizedGroups
                as $groupIndex => $group
            ) {

                foreach ($group['values'] as $value) {

                    if (
                        !isset($value['name'])
                        || !filled($value['name'])
                    ) {
                        continue;
                    }

                    if (
                        !isset($value['image'])
                        || !filled($value['image'])
                    ) {
                        continue;
                    }

                    $valueKey = strtolower(
                        trim((string) $value['name'])
                    );

                    if (!isset($imagesByValue[$groupIndex])) {
                        $imagesByValue[$groupIndex] = [];
                    }

                    if (!isset(
                        $imagesByValue[$groupIndex][$valueKey]
                    )) {
                        $imagesByValue[$groupIndex][$valueKey] = [];
                    }

                    $imagesByValue[$groupIndex][$valueKey][] =
                        $value['image'];
                }
            }

            foreach ($allVariants as $variant) {

                $attributes = is_array(
                    $variant->attributes
                )
                    ? array_values(
                        $variant->attributes
                    )
                    : [];

                $images =
                    $variantImages[$variant->id]
                    ?? [];

                if (empty($images)) {
                    continue;
                }

                foreach ($attributes as $index => $value) {

                    if (!filled($value)) {
                        continue;
                    }

                    $valueKey = strtolower(
                        trim((string) $value)
                    );

                    if (!isset($imagesByValue[$index])) {
                        $imagesByValue[$index] = [];
                    }

                    if (!isset(
                        $imagesByValue[$index][$valueKey]
                    )) {
                        $imagesByValue[$index][$valueKey] = [];
                    }

                    $imagesByValue[$index][$valueKey] =
                        array_values(
                            array_unique(
                                array_merge(
                                    $imagesByValue[$index][$valueKey],
                                    $images
                                )
                            )
                        );
                }
            }

            foreach ($imagesByValue as $index => $values) {

                foreach ($values as $key => $images) {

                    $imagesByValue[$index][$key] =
                        array_values(
                            array_unique(
                                array_filter($images)
                            )
                        );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | VARIANT DATA
            |--------------------------------------------------------------------------
            */

            $variants = $allVariants
                ->map(function ($variant) use ($variantImages) {

                    $attributes = is_array(
                        $variant->attributes
                    )
                        ? array_values(
                            $variant->attributes
                        )
                        : [];

                    $label = collect($attributes)
                        ->filter(function ($value) {
                            return filled($value);
                        })
                        ->implode(' / ');

                    return [

                        'id' =>
                            $variant->id,

                        'attributes' =>
                            $attributes,

                        'label' =>
                            $label ?: '-',

                        'sku_variant' =>
                            $variant->sku_variant,

                        'price' =>
                            $variant->price !== null
                                ? (int) $variant->price
                                : null,

                        'stock' =>
                            (int) $variant->stock,

                        'images' =>
                            $variantImages[$variant->id]
                            ?? [],
                    ];
                })
                ->values()
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | PRODUCT IMAGE
            |--------------------------------------------------------------------------
            */

            $productImage = null;

            if (filled($product->image)) {

                $productImage =
                    $this->normalizeImageUrl(
                        $product->image
                    );
            }

            if (!$productImage) {

                foreach ($allVariants as $variant) {

                    $images =
                        $variantImages[$variant->id]
                        ?? [];

                    if (!empty($images)) {

                        $productImage =
                            $images[0];

                        break;
                    }
                }
            }

            if (!$productImage) {

                foreach ($normalizedGroups as $group) {

                    foreach ($group['values'] as $value) {

                        if (
                            filled(
                                $value['image']
                                ?? null
                            )
                        ) {

                            $productImage =
                                $value['image'];

                            break 2;
                        }
                    }
                }
            }

            return [

                'id' =>
                    $product->id,

                'name' =>
                    $product->name,

                'sku' =>
                    $product->sku,

                'price' =>
                    (int) $product->price,

                'unit' =>
                    $product->unit,

                'stock' =>
                    (int) $product->stock,

                'image' =>
                    $productImage,

                'category' =>
                    $product->category->name
                    ?? 'Tanpa Kategori',

                'variant_groups' =>
                    $normalizedGroups,

                'images_by_value' =>
                    $imagesByValue,

                'variants' =>
                    $variants,
            ];
        })
            ->values();

        $customers =
            Customer::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | OPEN INVOICE YANG AKAN DIBUKA DARI /pos?open_invoice=ID
        |--------------------------------------------------------------------------
        */

        $openInvoiceId =
            $request->get('open_invoice');

        return view(
            'kasir.pos.index',
            compact(
                'products',
                'productsJson',
                'customers',
                'openInvoiceId'
            )
        );
    }


    /**
     * ============================================================
     * NORMALIZE IMAGE URL
     * ============================================================
     */
    private function normalizeImageUrl(?string $path): ?string
    {
        if (!filled($path)) {
            return null;
        }

        $path = trim((string) $path);

        if (
            Str::startsWith(
                $path,
                ['http://', 'https://']
            )
        ) {
            return $path;
        }

        if (
            Str::startsWith(
                $path,
                '/storage/'
            )
        ) {
            return asset(
                ltrim($path, '/')
            );
        }

        if (
            Str::startsWith(
                $path,
                'storage/'
            )
        ) {
            return asset($path);
        }

        return asset(
            'storage/' .
            ltrim($path, '/')
        );
    }


    /**
     * ============================================================
     * SIMPAN TRANSAKSI NORMAL
     * ============================================================
     */
    public function store(Request $request)
    {
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

        if (
            $data['payment_method'] === 'credit'
        ) {

            if (
                empty($data['customer_name'])
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'customer_name' =>
                            'Nama customer wajib diisi untuk transaksi kredit.',
                    ]);
            }

            if (
                empty($data['customer_phone'])
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'customer_phone' =>
                            'Nomor HP customer wajib diisi untuk transaksi kredit.',
                    ]);
            }

            if (
                empty($data['due_date'])
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'due_date' =>
                            'Tanggal jatuh tempo wajib diisi untuk transaksi kredit.',
                    ]);
            }
        }

        $transaction = DB::transaction(
            function () use ($data) {

                $total = 0;

                $itemsData = [];

                foreach (
                    $data['items']
                    as $item
                ) {

                    $product =
                        Product::lockForUpdate()
                            ->findOrFail(
                                $item['product_id']
                            );

                    $variant =
                        ProductVariant::lockForUpdate()
                            ->where(
                                'id',
                                $item['product_variant_id']
                            )
                            ->where(
                                'product_id',
                                $product->id
                            )
                            ->firstOrFail();

                    if (
                        (int) $variant->stock <
                        (int) $item['quantity']
                    ) {

                        $attributes =
                            is_array(
                                $variant->attributes
                            )
                                ? array_values(
                                    $variant->attributes
                                )
                                : [];

                        $label =
                            collect($attributes)
                                ->filter(function ($value) {
                                    return filled($value);
                                })
                                ->implode(' / ');

                        throw ValidationException::withMessages([
                            'items' =>
                                'Stok ' .
                                $product->name .
                                ' - ' .
                                ($label ?: '-') .
                                ' tidak cukup. ' .
                                'Stok tersedia: ' .
                                $variant->stock,
                        ]);
                    }

                    $price =
                        (
                            $variant->price !== null &&
                            (int) $variant->price > 0
                        )
                            ? (int) $variant->price
                            : (int) $product->price;

                    $subtotal =
                        $price *
                        (int) $item['quantity'];

                    $total += $subtotal;

                    $attributes =
                        is_array(
                            $variant->attributes
                        )
                            ? array_values(
                                $variant->attributes
                            )
                            : [];

                    $variantLabel =
                        collect($attributes)
                            ->filter(function ($value) {
                                return filled($value);
                            })
                            ->implode(' / ');

                    $itemsData[] = [

                        'product' =>
                            $product,

                        'variant' =>
                            $variant,

                        'quantity' =>
                            (int) $item['quantity'],

                        'price' =>
                            $price,

                        'subtotal' =>
                            $subtotal,

                        'variant_label' =>
                            $variantLabel ?: '-',
                    ];
                }

                $customerId = null;

                if (
                    $data['payment_method'] === 'credit'
                ) {

                    $customer =
                        Customer::where(
                            'phone',
                            $data['customer_phone']
                        )->first();

                    if (!$customer) {

                        $customer =
                            Customer::create([
                                'name' =>
                                    $data['customer_name'],

                                'phone' =>
                                    $data['customer_phone'],
                            ]);

                    } else {

                        $customer->update([
                            'name' =>
                                $data['customer_name'],
                        ]);
                    }

                    $customerId =
                        $customer->id;

                    $paid = 0;
                    $change = 0;
                    $status = 'on_hold';

                } else {

                    $paid =
                        (int) $data['paid'];

                    if (
                        $paid < $total
                    ) {

                        throw ValidationException::withMessages([
                            'paid' =>
                                'Uang bayar kurang dari total belanja.',
                        ]);
                    }

                    $change =
                        $paid - $total;

                    $status =
                        'paid';
                }

                $invoiceNumber =
                    'INV-' .
                    now()->format('Ymd') .
                    '-' .
                    Str::upper(
                        Str::random(5)
                    );

                $transaction =
                    Transaction::create([

                        'invoice_number' =>
                            $invoiceNumber,

                        'type' =>
                            'sale',

                        'user_id' =>
                            Auth::id(),

                        'customer_id' =>
                            $customerId,

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

                foreach (
                    $itemsData
                    as $entry
                ) {

                    $product =
                        $entry['product'];

                    $variant =
                        $entry['variant'];

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
                            $entry['variant_label'],

                        'price' =>
                            $entry['price'],

                        'quantity' =>
                            $entry['quantity'],

                        'subtotal' =>
                            $entry['subtotal'],
                    ]);

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

                    $variant->decrement(
                        'stock',
                        $entry['quantity']
                    );

                    $newTotalStock =
                        ProductVariant::where(
                            'product_id',
                            $product->id
                        )->sum('stock');

                    $product->update([
                        'stock' =>
                            $newTotalStock,
                    ]);
                }

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
            }
        );

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
     * RECEIPT
     * ============================================================
     */
    public function receipt(
        Transaction $transaction
    ) {
        $transaction->load([
            'items.product',
            'items.variant',
            'user',
            'customer',
            'payments.user',
        ]);

        return view(
            'kasir.pos.receipt',
            compact('transaction')
        );
    }


    /**
 * ============================================================
 * HISTORY
 *
 * HANYA TRANSAKSI SALE YANG SUDAH PAID
 * OPEN INVOICE ON HOLD TIDAK MASUK SINI
 * ============================================================
 */
public function history(Request $request)
{
    $validated = $request->validate([
        'date_from' => [
            'nullable',
            'date',
        ],

        'date_to' => [
            'nullable',
            'date',
            'after_or_equal:date_from',
        ],
    ]);

    $query = Transaction::with([
        'user',
        'customer',
    ])
        ->where('type', 'sale')
        ->where('status', 'paid');

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if (!empty($validated['date_from'])) {

        $query->whereDate(
            'created_at',
            '>=',
            $validated['date_from']
        );
    }

    if (!empty($validated['date_to'])) {

        $query->whereDate(
            'created_at',
            '<=',
            $validated['date_to']
        );
    }

    $transactions = $query
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view(
        'kasir.transactions.index',
        [
            'transactions' => $transactions,

            'dateFrom' =>
                $validated['date_from'] ?? null,

            'dateTo' =>
                $validated['date_to'] ?? null,
        ]
    );
}

/**
 * ============================================================
 * HISTORY - CETAK PDF
 * ============================================================
 */
public function historyPdf(Request $request)
{
    $validated = $request->validate([
        'date_from' => [
            'nullable',
            'date',
        ],

        'date_to' => [
            'nullable',
            'date',
            'after_or_equal:date_from',
        ],
    ]);

    $query = Transaction::with([
        'user',
    ])
        ->where('type', 'sale')
        ->where('status', 'paid');

    /*
    |--------------------------------------------------------------------------
    | FILTER TANGGAL
    |--------------------------------------------------------------------------
    */

    if (!empty($validated['date_from'])) {

        $query->whereDate(
            'created_at',
            '>=',
            $validated['date_from']
        );
    }

    if (!empty($validated['date_to'])) {

        $query->whereDate(
            'created_at',
            '<=',
            $validated['date_to']
        );
    }

    $transactions = $query
        ->latest()
        ->get();

    $totalOmzet = $transactions->sum('total');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'kasir.transactions.pdf',
        [
            'transactions' => $transactions,

            'totalOmzet' =>
                $totalOmzet,

            'dateFrom' =>
                $validated['date_from'] ?? null,

            'dateTo' =>
                $validated['date_to'] ?? null,
        ]
    );

    return $pdf->stream(
        'riwayat-transaksi.pdf'
    );
}

    /**
     * ============================================================
     * CREDIT INDEX
     * ============================================================
     */
    public function creditIndex()
    {
        $transactions =
            Transaction::with([
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
     * CREDIT SHOW
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
     * CREDIT PAYMENT
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

        DB::transaction(
            function () use (
                $data,
                $transaction
            ) {

                $transaction =
                    Transaction::lockForUpdate()
                        ->findOrFail(
                            $transaction->id
                        );

                if (
                    $transaction->payment_method !==
                    'credit'
                ) {

                    throw ValidationException::withMessages([
                        'transaction' =>
                            'Transaksi ini bukan transaksi kredit.',
                    ]);
                }

                if (
                    $transaction->status ===
                    'paid'
                ) {

                    throw ValidationException::withMessages([
                        'amount' =>
                            'Invoice ini sudah lunas.',
                    ]);
                }

                $remaining =
                    max(
                        0,
                        $transaction->total -
                        $transaction->paid
                    );

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
                        $data['note']
                        ??
                        'Pembayaran cicilan invoice kredit',
                ]);

                $newPaid =
                    $transaction->paid +
                    $data['amount'];

                $newStatus =
                    $newPaid >=
                    $transaction->total
                        ? 'paid'
                        : 'partial';

                $transaction->update([

                    'paid' =>
                        $newPaid,

                    'change' =>
                        0,

                    'status' =>
                        $newStatus,
                ]);
            }
        );

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


    /*
    |--------------------------------------------------------------------------
    | OPEN INVOICE
    |--------------------------------------------------------------------------
    */


    /**
     * ============================================================
     * OPEN INVOICE INDEX
     * ============================================================
     */
    public function openInvoiceIndex()
    {
        $transactions = Transaction::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->where(
                'type',
                'open_invoice'
            )
            ->where(
                'status',
                'on_hold'
            )
            ->with([
                'items',
                'payments',
            ])
            ->latest()
            ->get();

        return view(
            'kasir.open-invoice.index',
            [
                'transactions' =>
                    $transactions,
            ]
        );
    }


    /**
     * ============================================================
     * OPEN INVOICE - BUAT + AMBIL BARANG PERTAMA
     * ============================================================
     */
    public function hold(Request $request)
    {
        $validated = $request->validate([

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        return DB::transaction(
            function () use ($validated) {

                $itemsData = [];
                $total = 0;

                foreach (
                    $validated['items']
                    as $item
                ) {

                    $product =
                        Product::lockForUpdate()
                            ->findOrFail(
                                $item['product_id']
                            );

                    $variant =
                        ProductVariant::with('product')
                            ->whereKey(
                                $item['product_variant_id']
                            )
                            ->lockForUpdate()
                            ->firstOrFail();

                    if (
                        (int) $variant->product_id !==
                        (int) $product->id
                    ) {

                        throw ValidationException::withMessages([
                            'items' => [
                                'Variant produk tidak valid.',
                            ],
                        ]);
                    }

                    $quantity =
                        (int) $item['quantity'];

                    $stock =
                        (int) $variant->stock;

                    if (
                        $quantity > $stock
                    ) {

                        throw ValidationException::withMessages([
                            'items' => [
                                "Stok {$product->name} tidak cukup. " .
                                "Tersedia {$stock}, " .
                                "diminta {$quantity}.",
                            ],
                        ]);
                    }

                    $price = ($variant->price !== null &&(int) $variant->price > 0)
                        ? (int) $variant->price
                        : (int) $product->price;

                    $subtotal =
                        $price *
                        $quantity;

                    $attributes =
                        is_array(
                            $variant->attributes
                        )
                            ? array_values(
                                $variant->attributes
                            )
                            : [];

                    $variantLabel =
                        collect($attributes)
                            ->filter(function ($value) {
                                return filled($value);
                            })
                            ->implode(' / ');

                    $itemsData[] = [

                        'product' =>
                            $product,

                        'variant' =>
                            $variant,

                        'quantity' =>
                            $quantity,

                        'price' =>
                            $price,

                        'subtotal' =>
                            $subtotal,

                        'variant_label' =>
                            $variantLabel ?: '-',
                    ];

                    $total += $subtotal;
                }

                $transaction =
                    Transaction::create([

                        'invoice_number' =>
                            $this->generateOpenInvoiceNumber(),

                        'type' =>
                            'open_invoice',

                        'user_id' =>
                            Auth::id(),

                        'customer_id' =>
                            null,

                        'total' =>
                            $total,

                        'paid' =>
                            0,

                        'change' =>
                            0,

                        'payment_method' =>
                            'cash',

                        'status' =>
                            'on_hold',

                        'due_date' =>
                            null,
                    ]);

                foreach (
                    $itemsData
                    as $entry
                ) {

                    $this->addOpenInvoiceItem(
                        $transaction,
                        $entry['product'],
                        $entry['variant'],
                        $entry['quantity'],
                        $entry['price'],
                        $entry['subtotal'],
                        $entry['variant_label']
                    );
                }

                return response()->json([

                    'success' =>
                        true,

                    'message' =>
                        'Open Invoice berhasil dibuat dan barang berhasil diambil.',

                    'data' => [

                        'id' =>
                            $transaction->id,

                        'invoice_number' =>
                            $transaction->invoice_number,

                        'total' =>
                            (int) $transaction->total,

                        'paid' =>
                            0,

                        'remaining' =>
                            (int) $transaction->total,
                    ],
                ]);
            }
        );
    }


    /**
     * ============================================================
     * OPEN INVOICE - AMBIL BARANG LAGI
     * ============================================================
     */
    public function openInvoiceTake(
        Request $request,
        Transaction $transaction
    ) {
        if (
            $transaction->user_id !== Auth::id()
            || !$transaction->isOpenInvoice()
        ) {
            abort(404);
        }

        $validated = $request->validate([

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.product_variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        return DB::transaction(
            function () use (
                $validated,
                $transaction
            ) {

                $transaction =
                    Transaction::query()
                        ->whereKey(
                            $transaction->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    $transaction->user_id !== Auth::id()
                    || !$transaction->isOpenInvoice()
                ) {

                    throw ValidationException::withMessages([
                        'transaction' => [
                            'Open Invoice sudah tidak tersedia.',
                        ],
                    ]);
                }

                $additionalTotal = 0;

                foreach (
                    $validated['items']
                    as $item
                ) {

                    $product =
                        Product::lockForUpdate()
                            ->findOrFail(
                                $item['product_id']
                            );

                    $variant =
                        ProductVariant::with('product')
                            ->whereKey(
                                $item['product_variant_id']
                            )
                            ->lockForUpdate()
                            ->firstOrFail();

                    if (
                        (int) $variant->product_id !==
                        (int) $product->id
                    ) {

                        throw ValidationException::withMessages([
                            'items' => [
                                'Variant produk tidak valid.',
                            ],
                        ]);
                    }

                    $quantity =
                        (int) $item['quantity'];

                    $stock =
                        (int) $variant->stock;

                    if (
                        $quantity > $stock
                    ) {

                        $attributes =
                            is_array(
                                $variant->attributes
                            )
                                ? array_values(
                                    $variant->attributes
                                )
                                : [];

                        $label =
                            collect($attributes)
                                ->filter(function ($value) {
                                    return filled($value);
                                })
                                ->implode(' / ');

                        throw ValidationException::withMessages([
                            'items' => [
                                "Stok {$product->name} " .
                                ($label
                                    ? "- {$label} "
                                    : '') .
                                "tidak cukup. " .
                                "Tersedia {$stock}, " .
                                "diminta {$quantity}.",
                            ],
                        ]);
                    }

                    $price = ($variant->price !== null &&(int) $variant->price > 0)
                        ? (int) $variant->price
                        : (int) $product->price;

                    $subtotal = $price * $quantity;

                    $subtotal =
                        $price *
                        $quantity;

                    $attributes =
                        is_array(
                            $variant->attributes
                        )
                            ? array_values(
                                $variant->attributes
                            )
                            : [];

                    $variantLabel =
                        collect($attributes)
                            ->filter(function ($value) {
                                return filled($value);
                            })
                            ->implode(' / ');

                    $this->addOpenInvoiceItem(
                        $transaction,
                        $product,
                        $variant,
                        $quantity,
                        $price,
                        $subtotal,
                        $variantLabel ?: '-'
                    );

                    $additionalTotal += $subtotal;
                }

                $newTotal =
                    (int) $transaction->total +
                    $additionalTotal;

                $transaction->update([
                    'total' =>
                        $newTotal,
                ]);

                return response()->json([

                    'success' =>
                        true,

                    'message' =>
                        'Barang berhasil ditambahkan ke Open Invoice.',

                    'data' => [

                        'id' =>
                            $transaction->id,

                        'invoice_number' =>
                            $transaction->invoice_number,

                        'total' =>
                            $newTotal,

                        'paid' =>
                            (int) $transaction->paid,

                        'remaining' =>
                            max(
                                0,
                                $newTotal -
                                (int) $transaction->paid
                            ),
                    ],
                ]);
            }
        );
    }


    /**
     * ============================================================
     * HELPER TAMBAH ITEM OPEN INVOICE
     * ============================================================
     */
    private function addOpenInvoiceItem(
        Transaction $transaction,
        Product $product,
        ProductVariant $variant,
        int $quantity,
        int $price,
        int $subtotal,
        string $variantLabel
    ): void {

        $transaction->items()->create([

            'product_id' =>
                $product->id,

            'product_variant_id' =>
                $variant->id,

            'product_name' =>
                $product->name,

            'variant_label' =>
                $variantLabel,

            'price' =>
                $price,

            'quantity' =>
                $quantity,

            'subtotal' =>
                $subtotal,
        ]);

        $variant->stock =
            (int) $variant->stock -
            $quantity;

        $variant->save();

        $this->syncProductStock(
            $product
        );

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
                $quantity,

            'source' =>
                'open_invoice',

            'transaction_id' =>
                $transaction->id,

            'note' =>
                'Pengambilan Open Invoice ' .
                $transaction->invoice_number,
        ]);
    }


    /**
     * ============================================================
     * SYNC TOTAL STOCK PRODUCT
     * ============================================================
     */
    private function syncProductStock(
        Product $product
    ): void {

        if (
            method_exists(
                $product,
                'syncTotalStock'
            )
        ) {

            $product->syncTotalStock();

            return;
        }

        $product->stock =
            ProductVariant::where(
                'product_id',
                $product->id
            )->sum('stock');

        $product->save();
    }


    /**
     * ============================================================
     * GENERATE NOMOR OPEN INVOICE
     * ============================================================
     */
    private function generateOpenInvoiceNumber(): string
    {
        do {

            $number =
                'OPEN-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    Str::random(5)
                );

        } while (
            Transaction::where(
                'invoice_number',
                $number
            )->exists()
        );

        return $number;
    }


    /**
     * ============================================================
     * OPEN INVOICE - DATA
     * ============================================================
     */
    public function openInvoices()
    {
        $transactions = Transaction::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->where(
                'type',
                'open_invoice'
            )
            ->where(
                'status',
                'on_hold'
            )
            ->with([
                'items',
                'payments',
            ])
            ->latest()
            ->get();

        return response()->json([

            'success' =>
                true,

            'data' =>
                $transactions
                    ->map(function ($transaction) {

                        $paid =
                            (int) $transaction->paid;

                        $total =
                            (int) $transaction->total;

                        return [

                            'id' =>
                                $transaction->id,

                            'invoice_number' =>
                                $transaction->invoice_number,

                            'total' =>
                                $total,

                            'paid' =>
                                $paid,

                            'remaining' =>
                                max(
                                    0,
                                    $total - $paid
                                ),

                            'status' =>
                                $transaction->status,

                            'item_count' =>
                                $transaction
                                    ->items
                                    ->sum('quantity'),

                            'created_at' =>
                                $transaction->created_at
                                    ?->format(
                                        'd/m/Y H:i'
                                    )
                                ?? '-',
                        ];
                    })
                    ->values(),
        ]);
    }


    /**
     * ============================================================
     * OPEN INVOICE - DETAIL
     * ============================================================
     */
    public function openInvoiceShow(
        Transaction $transaction
    ) {
        if (
            $transaction->user_id !== Auth::id()
            || !$transaction->isOpenInvoice()
        ) {
            abort(404);
        }

        $transaction->load([
            'items.product',
            'items.variant',
            'payments.user',
        ]);

        $paid =
            (int) $transaction->paid;

        $total =
            (int) $transaction->total;

        return response()->json([

            'success' =>
                true,

            'data' => [

                'id' =>
                    $transaction->id,

                'invoice_number' =>
                    $transaction->invoice_number,

                'total' =>
                    $total,

                'paid' =>
                    $paid,

                'remaining' =>
                    max(
                        0,
                        $total - $paid
                    ),

                'status' =>
                    $transaction->status,

                'items' =>
                    $transaction->items
                        ->map(function ($item) {

                            $variant =
                                $item->variant;

                            $product =
                                $item->product;

                            $image = null;

                            if (
                                $variant
                                && method_exists(
                                    $variant,
                                    'images'
                                )
                            ) {

                                $images =
                                    $variant
                                        ->images()
                                        ->get();

                                if (
                                    $images->count()
                                ) {

                                    $first =
                                        $images->first();

                                    $image =
                                        $first->image
                                        ?? $first->path
                                        ?? $first->url
                                        ?? null;
                                }
                            }

                            if (
                                !$image
                                && $product
                            ) {

                                $image =
                                    $product->image;
                            }

                            return [

                                'id' =>
                                    $item->id,

                                'key' =>
                                    $item->product_id .
                                    '-' .
                                    $item->product_variant_id .
                                    '-' .
                                    $item->id,

                                'product_id' =>
                                    $item->product_id,

                                'product_variant_id' =>
                                    $item->product_variant_id,

                                'name' =>
                                    $item->product_name,

                                'variant' =>
                                    $item->variant_label,

                                'price' =>
                                    (int) $item->price,

                                'quantity' =>
                                    (int) $item->quantity,

                                'subtotal' =>
                                    (int) $item->subtotal,

                                'stock' =>
                                    $variant
                                        ? (int) $variant->stock
                                        : 0,

                                'sku_variant' =>
                                    $variant?->sku_variant,

                                'attributes' =>
                                    $variant
                                    && is_array(
                                        $variant->attributes
                                    )
                                        ? $variant->attributes
                                        : [],

                                'image' =>
                                    $image
                                        ? $this->normalizeTransactionImage(
                                            $image
                                        )
                                        : null,
                            ];
                        })
                        ->values(),

                'payments' =>
                    $transaction->payments
                        ->map(function ($payment) {

                            return [

                                'id' =>
                                    $payment->id,

                                'amount' =>
                                    (int) $payment->amount,

                                'payment_method' =>
                                    $payment->payment_method,

                                'note' =>
                                    $payment->note,

                                'created_at' =>
                                    $payment->created_at
                                        ?->format(
                                            'd/m/Y H:i'
                                        )
                                    ?? '-',
                            ];
                        })
                        ->values(),
            ],
        ]);
    }


    /**
     * ============================================================
     * NORMALIZE TRANSACTION IMAGE
     * ============================================================
     */
    private function normalizeTransactionImage(
        $image
    ): ?string {

        if (!$image) {
            return null;
        }

        $image =
            trim((string) $image);

        if (
            str_starts_with(
                $image,
                'http://'
            )
            ||
            str_starts_with(
                $image,
                'https://'
            )
        ) {
            return $image;
        }

        if (
            str_starts_with(
                $image,
                '/storage/'
            )
        ) {
            return $image;
        }

        if (
            str_starts_with(
                $image,
                'storage/'
            )
        ) {
            return '/' . $image;
        }

        return '/storage/' .
            ltrim(
                $image,
                '/'
            );
    }


    /**
     * ============================================================
     * OPEN INVOICE - BAYAR
     *
     * Bisa bayar berkali-kali.
     *
     * Jika belum lunas:
     *   type   = open_invoice
     *   status = on_hold
     *
     * Jika lunas:
     *   type   = sale
     *   status = paid
     *
     * Stock TIDAK berubah di sini.
     * Stock sudah dikurangi ketika barang diambil.
     * ============================================================
     */
    public function openInvoiceCheckout(
    Request $request,
    Transaction $transaction
) {
    if (
        $transaction->user_id !== Auth::id()
        || !$transaction->isOpenInvoice()
    ) {
        abort(404);
    }

    $validated = $request->validate([

        'payment_method' => [
            'required',
            'in:cash,qris',
        ],

        'paid' => [
            'required',
            'integer',
            'min:1',
        ],

        'note' => [
            'nullable',
            'string',
            'max:1000',
        ],
    ]);

    DB::transaction(
        function () use (
            $validated,
            $transaction
        ) {

            $transaction =
                Transaction::query()
                    ->whereKey(
                        $transaction->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

            if (
                $transaction->user_id !== Auth::id()
                || !$transaction->isOpenInvoice()
            ) {

                throw ValidationException::withMessages([
                    'transaction' => [
                        'Open Invoice sudah tidak tersedia.',
                    ],
                ]);
            }

            $total =
                (int) $transaction->total;

            $alreadyPaid =
                (int) $transaction->paid;

            $remaining =
                max(
                    0,
                    $total -
                    $alreadyPaid
                );

            if ($remaining <= 0) {

                throw ValidationException::withMessages([
                    'paid' => [
                        'Open Invoice ini sudah lunas.',
                    ],
                ]);
            }

            $paymentAmount =
                (int) $validated['paid'];

            $paymentMethod =
                $validated['payment_method'];

            /*
            |--------------------------------------------------------------------------
            | VALIDASI PEMBAYARAN
            |--------------------------------------------------------------------------
            |
            | CASH:
            | Boleh membayar lebih dari sisa tagihan.
            | Selisihnya menjadi kembalian.
            |
            | QRIS:
            | Tidak boleh membayar lebih dari sisa tagihan.
            |
            |--------------------------------------------------------------------------
            */

            if (
                $paymentMethod !== 'cash'
                && $paymentAmount > $remaining
            ) {

                throw ValidationException::withMessages([
                    'paid' => [
                        'Jumlah pembayaran melebihi sisa tagihan. ' .
                        'Sisa tagihan: Rp ' .
                        number_format(
                            $remaining,
                            0,
                            ',',
                            '.'
                        ),
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | HITUNG PEMBAYARAN YANG DITERAPKAN KE INVOICE
            |--------------------------------------------------------------------------
            */

            $appliedAmount =
                min(
                    $paymentAmount,
                    $remaining
                );

            /*
            |--------------------------------------------------------------------------
            | HITUNG KEMBALIAN
            |--------------------------------------------------------------------------
            */

            $change =
                $paymentMethod === 'cash'
                    ? max(
                        0,
                        $paymentAmount - $remaining
                    )
                    : 0;

            /*
            |--------------------------------------------------------------------------
            | SIMPAN PAYMENT
            |--------------------------------------------------------------------------
            |
            | Yang disimpan sebagai payment adalah nominal uang yang benar-benar
            | dibayarkan customer.
            |
            |--------------------------------------------------------------------------
            */

            TransactionPayment::create([

                'transaction_id' =>
                    $transaction->id,

                'user_id' =>
                    Auth::id(),

                'amount' =>
                    $paymentAmount,

                'payment_method' =>
                    $paymentMethod,

                'note' =>
                    $validated['note']
                    ??
                    'Pembayaran Open Invoice',
            ]);

            /*
            |--------------------------------------------------------------------------
            | HITUNG TOTAL YANG SUDAH DILUNASI
            |--------------------------------------------------------------------------
            */

            $newPaid =
                $alreadyPaid +
                $appliedAmount;

            /*
            |--------------------------------------------------------------------------
            | LUNAS
            |--------------------------------------------------------------------------
            */

            if (
                $newPaid >=
                $total
            ) {

                $transaction->update([

                    'type' =>
                        'sale',

                    'paid' =>
                        $newPaid,

                    'change' =>
                        $change,

                    'payment_method' =>
                        $paymentMethod,

                    'status' =>
                        'paid',

                    'due_date' =>
                        null,
                ]);

            } else {

                /*
                |--------------------------------------------------------------------------
                | BELUM LUNAS
                |--------------------------------------------------------------------------
                */

                $transaction->update([

                    'paid' =>
                        $newPaid,

                    'change' =>
                        0,

                    'payment_method' =>
                        $paymentMethod,

                    'status' =>
                        'on_hold',
                ]);
            }
        }
    );

    $transaction->refresh();

    /*
    |--------------------------------------------------------------------------
    | JIKA LUNAS
    |--------------------------------------------------------------------------
    */

    if (
        $transaction->status === 'paid'
    ) {

        return redirect()
            ->route(
                'pos.receipt',
                $transaction
            )
            ->with(
                'success',
                'Open Invoice berhasil dilunasi.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | BELUM LUNAS
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route(
            'pos.open-invoice.index'
        )
        ->with(
            'success',
            'Pembayaran berhasil disimpan. ' .
            'Sisa tagihan: Rp ' .
            number_format(
                max(
                    0,
                    (int) $transaction->total -
                    (int) $transaction->paid
                ),
                0,
                ',',
                '.'
            )
        );
}

    /**
     * ============================================================
     * OPEN INVOICE - CANCEL
     *
     * Semua barang yang sudah diambil:
     * - stok dikembalikan
     * - stock movement IN dibuat
     *
     * Cancel hanya diperbolehkan jika belum ada pembayaran.
     *
     * Transaction items TIDAK dihapus.
     * ============================================================
     */
    public function openInvoiceCancel(
        Transaction $transaction
    ) {
        if (
            $transaction->user_id !== Auth::id()
            || !$transaction->isOpenInvoice()
        ) {
            abort(404);
        }

        DB::transaction(
            function () use ($transaction) {

                $transaction =
                    Transaction::query()
                        ->whereKey(
                            $transaction->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (
                    $transaction->user_id !== Auth::id()
                    || !$transaction->isOpenInvoice()
                ) {

                    throw ValidationException::withMessages([
                        'transaction' => [
                            'Open Invoice sudah tidak tersedia.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | TIDAK BOLEH CANCEL JIKA SUDAH ADA PEMBAYARAN
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $transaction->paid > 0
                ) {

                    throw ValidationException::withMessages([
                        'transaction' => [
                            'Open Invoice yang sudah memiliki pembayaran tidak dapat dibatalkan.',
                        ],
                    ]);
                }

                $transaction->load([
                    'items',
                ]);

                /*
                |--------------------------------------------------------------------------
                | KEMBALIKAN SEMUA STOK
                |--------------------------------------------------------------------------
                */

                foreach (
                    $transaction->items
                    as $item
                ) {

                    if (!$item->product_variant_id) {
                        continue;
                    }

                    $variant =
                        ProductVariant::lockForUpdate()
                            ->find(
                                $item->product_variant_id
                            );

                    if (!$variant) {
                        continue;
                    }

                    $quantity =
                        (int) $item->quantity;

                    /*
                    |--------------------------------------------------------------------------
                    | STOCK IN
                    |--------------------------------------------------------------------------
                    */

                    $variant->stock =
                        (int) $variant->stock +
                        $quantity;

                    $variant->save();

                    /*
                    |--------------------------------------------------------------------------
                    | SYNC PRODUCT STOCK
                    |--------------------------------------------------------------------------
                    */

                    $product =
                        Product::lockForUpdate()
                            ->find(
                                $variant->product_id
                            );

                    if ($product) {

                        $this->syncProductStock(
                            $product
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STOCK MOVEMENT IN
                    |--------------------------------------------------------------------------
                    */

                    StockMovement::create([

                        'product_id' =>
                            $variant->product_id,

                        'product_variant_id' =>
                            $variant->id,

                        'user_id' =>
                            Auth::id(),

                        'type' =>
                            'in',

                        'quantity' =>
                            $quantity,

                        'source' =>
                            'open_invoice_cancel',

                        'transaction_id' =>
                            $transaction->id,

                        'note' =>
                            'Pengembalian barang Open Invoice ' .
                            $transaction->invoice_number .
                            ' karena invoice dibatalkan',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | JANGAN HAPUS TRANSACTION ITEMS
                |--------------------------------------------------------------------------
                */

                $transaction->update([

                    'status' =>
                        'cancelled',
                ]);
            }
        );

        return redirect()
            ->route(
                'pos.open-invoice.index'
            )
            ->with(
                'success',
                'Open Invoice dibatalkan dan stok barang berhasil dikembalikan.'
            );
    }
}
