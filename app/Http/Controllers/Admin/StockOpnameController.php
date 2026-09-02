<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.stock-opname.index', compact('opnames'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.stock-opname.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opname_date' => 'required|date',
            'note' => 'nullable|string',
            'products' => 'required|array|min:1',

            'products.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'products.*.physical_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'products.*.note' => [
                'nullable',
                'string',
            ],
        ]);

        DB::transaction(function () use ($data) {

            $opname = StockOpname::create([
                'user_id' => Auth::id(),
                'opname_date' => $data['opname_date'],
                'status' => 'completed',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['products'] as $item) {

                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);

                $systemStock = $product->stock;
                $physicalStock = $item['physical_stock'];

                $difference = $physicalStock - $systemStock;

                $opname->details()->create([
                    'product_id' => $product->id,
                    'system_stock' => $systemStock,
                    'physical_stock' => $physicalStock,
                    'difference' => $difference,
                    'note' => $item['note'] ?? null,
                ]);

                if ($difference !== 0) {

                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                        'type' => $difference > 0 ? 'in' : 'out',
                        'quantity' => abs($difference),
                        'source' => 'stock_opname',
                        'note' => 'Penyesuaian stock opname #'.$opname->id,
                    ]);

                    $product->update([
                        'stock' => $physicalStock,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.stock-opname.index')
            ->with('success', 'Stock opname berhasil disimpan.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load([
            'user',
            'details.product',
        ]);

        return view(
            'admin.stock-opname.show',
            compact('stockOpname')
        );
    }
}
