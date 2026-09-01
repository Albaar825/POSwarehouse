<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'required|string|unique:products,sku',
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
        ]);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
        ]);

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Barang berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
