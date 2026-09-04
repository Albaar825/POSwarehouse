<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;

class StockController extends Controller
{
    /**
     * Menampilkan riwayat stok masuk dan keluar.
     */
    public function index()
    {
        $movements = StockMovement::with([
            'product',
            'productVariant',
            'user',
        ])
            ->latest()
            ->get();

        return view(
            'admin.stock.index',
            compact('movements')
        );
    }
}
