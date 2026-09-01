<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $data = [];

        if ($user->isAdmin()) {
            $data['total_products'] = Product::count();
            $data['low_stock_count'] = 0; // sementara, aktifkan lagi setelah migration products lengkap
            $data['today_sales'] = 0; // sementara, aktifkan lagi setelah migration transactions lengkap
        }

        if ($user->isKasir()) {
            $data['my_transactions_today'] = Transaction::whereDate('created_at', today())->count();
        }

        return view('dashboard', $data);
    }
}
