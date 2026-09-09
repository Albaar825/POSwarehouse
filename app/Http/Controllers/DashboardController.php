<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $data = [];

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->isAdmin()) {

            // ==============================================================
            // SUMMARY
            // ==============================================================

            $data['total_products'] = Product::count();

            $data['total_stock'] = Product::with('variants')
                ->get()
                ->sum(fn ($product) => $product->totalStock());

            $data['low_stock_count'] = Product::with('variants')
                ->get()
                ->filter(fn ($product) => $product->isLowStock())
                ->count();

            $data['today_sales'] = Transaction::whereDate(
                'created_at',
                today()
            )->sum('total');

            $data['today_transactions'] = Transaction::whereDate(
                'created_at',
                today()
            )->count();

            /*
            |--------------------------------------------------------------------------
            | SALES CHART - 7 HARI TERAKHIR
            |--------------------------------------------------------------------------
            */

            $salesLast7Days = Transaction::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total) as total'),
                    DB::raw('COUNT(*) as transactions')
                )
                ->whereDate('created_at', '>=', now()->subDays(6))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $salesLabels = [];
            $salesData = [];
            $transactionData = [];

            for ($i = 6; $i >= 0; $i--) {

                $date = now()->subDays($i);

                $dateKey = $date->format('Y-m-d');

                $salesLabels[] = $date->translatedFormat('D, d M');

                $salesData[] = (int) (
                    $salesLast7Days[$dateKey]->total ?? 0
                );

                $transactionData[] = (int) (
                    $salesLast7Days[$dateKey]->transactions ?? 0
                );
            }

            $data['sales_labels'] = $salesLabels;
            $data['sales_data'] = $salesData;
            $data['transaction_data'] = $transactionData;

            /*
            |--------------------------------------------------------------------------
            | PENJUALAN BERDASARKAN KATEGORI
            |--------------------------------------------------------------------------
            */

            $salesByCategory = DB::table('transaction_items')
                ->join(
                    'transactions',
                    'transactions.id',
                    '=',
                    'transaction_items.transaction_id'
                )
                ->join(
                    'products',
                    'products.id',
                    '=',
                    'transaction_items.product_id'
                )
                ->leftJoin(
                    'categories',
                    'categories.id',
                    '=',
                    'products.category_id'
                )
                ->select(
                    DB::raw("COALESCE(categories.name, 'Tanpa Kategori') as category"),
                    DB::raw('SUM(transaction_items.subtotal) as total')
                )
                ->whereDate(
                    'transactions.created_at',
                    '>=',
                    now()->subDays(29)
                )
                ->groupBy(
                    DB::raw("COALESCE(categories.name, 'Tanpa Kategori')")
                )
                ->orderByDesc('total')
                ->get();

            $data['category_labels'] = $salesByCategory
                ->pluck('category')
                ->values();

            $data['category_data'] = $salesByCategory
                ->pluck('total')
                ->map(fn ($value) => (int) $value)
                ->values();

            /*
            |--------------------------------------------------------------------------
            | TOP 5 PRODUK TERLARIS
            |--------------------------------------------------------------------------
            */

            $topProducts = DB::table('transaction_items')
                ->join(
                    'transactions',
                    'transactions.id',
                    '=',
                    'transaction_items.transaction_id'
                )
                ->select(
                    'transaction_items.product_name',
                    DB::raw('SUM(transaction_items.quantity) as total_quantity'),
                    DB::raw('SUM(transaction_items.subtotal) as total_sales')
                )
                ->whereDate(
                    'transactions.created_at',
                    '>=',
                    now()->subDays(29)
                )
                ->groupBy('transaction_items.product_name')
                ->orderByDesc('total_quantity')
                ->limit(5)
                ->get();

            $data['top_product_labels'] = $topProducts
                ->pluck('product_name')
                ->values();

            $data['top_product_data'] = $topProducts
                ->pluck('total_quantity')
                ->map(fn ($value) => (int) $value)
                ->values();

            $data['top_products'] = $topProducts;

            /*
            |--------------------------------------------------------------------------
            | PRODUK STOK MENIPIS
            |--------------------------------------------------------------------------
            */

            $data['low_stock_products'] = Product::with('variants')
                ->where('is_active', true)
                ->get()
                ->filter(fn ($product) => $product->isLowStock())
                ->sortBy(fn ($product) => $product->totalStock())
                ->take(5);

            /*
            |--------------------------------------------------------------------------
            | TRANSAKSI TERBARU
            |--------------------------------------------------------------------------
            */

            $data['recent_transactions'] = Transaction::with('user')
                ->latest()
                ->limit(5)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | STOCK MOVEMENT TERBARU
            |--------------------------------------------------------------------------
            */

            $data['recent_stock_movements'] = StockMovement::with([
                    'product',
                    'user',
                ])
                ->latest()
                ->limit(5)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | STOCK OPNAME TERBARU
            |--------------------------------------------------------------------------
            */

            $data['recent_opnames'] = StockOpname::with('user')
                ->latest()
                ->limit(5)
                ->get();
        }

       /*
|--------------------------------------------------------------------------
| DASHBOARD KASIR
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| DASHBOARD KASIR
|--------------------------------------------------------------------------
*/

if ($user->isKasir()) {

    /*
    |--------------------------------------------------------------------------
    | SUMMARY HARI INI
    |--------------------------------------------------------------------------
    */

    // Jumlah transaksi hari ini
    $data['my_transactions_today'] =
        Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

    // Omzet hari ini
    $data['my_sales_today'] =
        Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'paid')
            ->sum('total');

    // Total item terjual hari ini
    $data['my_items_today'] =
        TransactionItem::join(
            'transactions',
            'transactions.id',
            '=',
            'transaction_items.transaction_id'
        )
        ->where('transactions.user_id', $user->id)
        ->whereDate('transactions.created_at', today())
        ->sum('transaction_items.quantity');


    /*
    |--------------------------------------------------------------------------
    | KREDIT AKTIF
    |--------------------------------------------------------------------------
    */

    $data['my_credit_count'] =
        Transaction::where('user_id', $user->id)
            ->where('payment_method', 'credit')
            ->whereIn('status', ['on_hold', 'partial'])
            ->count();


    /*
    |--------------------------------------------------------------------------
    | ALIAS UNTUK BLADE
    |--------------------------------------------------------------------------
    */

    $data['kasir_today_sales'] =
        $data['my_sales_today'];

    $data['kasir_items_today'] =
        $data['my_items_today'];


    /*
    |--------------------------------------------------------------------------
    | RATA-RATA TRANSAKSI
    |--------------------------------------------------------------------------
    */

    $transactionCount =
        Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'paid')
            ->count();

    $data['kasir_average_transaction'] =
        $transactionCount > 0
            ? $data['kasir_today_sales'] / $transactionCount
            : 0;


    /*
    |--------------------------------------------------------------------------
    | AKTIVITAS PENJUALAN - 7 HARI TERAKHIR
    |--------------------------------------------------------------------------
    */

    $kasirSalesLast7Days =
        Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as total'),
            DB::raw('COUNT(*) as transactions')
        )
        ->where('user_id', $user->id)
        ->where('status', 'paid')
        ->whereDate(
            'created_at',
            '>=',
            now()->subDays(6)
        )
        ->groupBy(
            DB::raw('DATE(created_at)')
        )
        ->orderBy('date')
        ->get()
        ->keyBy('date');


    $kasirSalesLabels = [];
    $kasirSalesData = [];
    $kasirTransactionData = [];

    for ($i = 6; $i >= 0; $i--) {

        $date = now()->subDays($i);

        $dateKey = $date->format('Y-m-d');

        $kasirSalesLabels[] =
            $date->translatedFormat('D, d M');

        $kasirSalesData[] =
            (int) (
                $kasirSalesLast7Days[$dateKey]->total ?? 0
            );

        $kasirTransactionData[] =
            (int) (
                $kasirSalesLast7Days[$dateKey]->transactions ?? 0
            );
    }

    $data['kasir_sales_labels'] =
        $kasirSalesLabels;

    $data['kasir_sales_data'] =
        $kasirSalesData;

    $data['kasir_transaction_data'] =
        $kasirTransactionData;


    /*
    |--------------------------------------------------------------------------
    | METODE PEMBAYARAN HARI INI
    |--------------------------------------------------------------------------
    */

    // CASH
    $cashQuery =
        Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'paid')
            ->where('payment_method', 'cash');

    $data['kasir_cash_count'] =
        $cashQuery->count();

    $data['kasir_cash_total'] =
        $cashQuery->sum('total');


    // QRIS
    $qrisQuery =
        Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('status', 'paid')
            ->where('payment_method', 'qris');

    $data['kasir_qris_count'] =
        $qrisQuery->count();

    $data['kasir_qris_total'] =
        $qrisQuery->sum('total');


    // CREDIT
    $creditQuery =
        Transaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->where('payment_method', 'credit');

    $data['kasir_credit_count'] =
        $creditQuery->count();

    $data['kasir_credit_total'] =
        $creditQuery->sum('total');


    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI TERBARU
    |--------------------------------------------------------------------------
    */

    $data['kasir_recent_transactions'] =
        Transaction::with('customer')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();


    /*
    |--------------------------------------------------------------------------
    | TOP 5 PRODUK
    |--------------------------------------------------------------------------
    */

    $topProducts =
        DB::table('transaction_items')
            ->join(
                'transactions',
                'transactions.id',
                '=',
                'transaction_items.transaction_id'
            )
            ->select(
                'transaction_items.product_name',
                DB::raw(
                    'SUM(transaction_items.quantity) as total'
                )
            )
            ->where(
                'transactions.user_id',
                $user->id
            )
            ->whereDate(
                'transactions.created_at',
                '>=',
                now()->subDays(29)
            )
            ->groupBy(
                'transaction_items.product_name'
            )
            ->orderByDesc('total')
            ->limit(5)
            ->get();

    $data['kasir_top_products'] =
        $topProducts;
}
        return view('dashboard', $data);
    }
}
