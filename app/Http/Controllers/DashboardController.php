<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
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

        if ($user->isKasir()) {

            $data['my_transactions_today'] = Transaction::where(
                'user_id',
                $user->id
            )
                ->whereDate('created_at', today())
                ->count();
        }

        return view('dashboard', $data);
    }
}
