<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $movements = StockMovement::with(['product', 'user'])->latest()->paginate(15);
        return view('admin.stock.index', compact('movements'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.stock.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            if ($data['type'] === 'out' && $product->stock < $data['quantity']) {
                abort(back()->withErrors(['quantity' => 'Stok tidak cukup. Sisa stok: '.$product->stock])->getTargetUrl());
            }

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'source' => 'manual',
                'note' => $data['note'] ?? null,
            ]);

            $product->stock += $data['type'] === 'in' ? $data['quantity'] : -$data['quantity'];
            $product->save();
        });

        return redirect()->route('admin.stock.index')->with('success', 'Stok berhasil dicatat.');
    }
}
