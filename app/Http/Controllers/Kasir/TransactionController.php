<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();
        return view('pos.index', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris',
            'paid' => 'required|integer|min:0',
        ]);

        $transaction = DB::transaction(function () use ($data) {
            $total = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    abort(back()->withErrors(['items' => 'Stok '.$product->name.' tidak cukup.'])->getTargetUrl());
                }

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            if ($data['paid'] < $total) {
                abort(back()->withErrors(['paid' => 'Uang bayar kurang dari total belanja.'])->getTargetUrl());
            }

            $transaction = Transaction::create([
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
                'user_id' => Auth::id(),
                'total' => $total,
                'paid' => $data['paid'],
                'change' => $data['paid'] - $total,
                'payment_method' => $data['payment_method'],
            ]);

            foreach ($itemsData as $entry) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $entry['product']->id,
                    'product_name' => $entry['product']->name,
                    'price' => $entry['product']->price,
                    'quantity' => $entry['quantity'],
                    'subtotal' => $entry['subtotal'],
                ]);

                StockMovement::create([
                    'product_id' => $entry['product']->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity' => $entry['quantity'],
                    'source' => 'transaction',
                    'transaction_id' => $transaction->id,
                ]);

                $entry['product']->decrement('stock', $entry['quantity']);
            }

            return $transaction;
        });

        return redirect()->route('pos.receipt', $transaction)->with('success', 'Transaksi berhasil.');
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('items', 'user');
        return view('pos.receipt', compact('transaction'));
    }

    public function history()
    {
        $transactions = Transaction::with('user')->latest()->paginate(15);
        return view('transactions.index', compact('transactions'));
    }
}
