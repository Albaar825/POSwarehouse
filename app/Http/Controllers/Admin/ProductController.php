<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'variants'])
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->latest()
            ->get();

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
            'image' => 'nullable|image|max:2048', // max 2MB
            'purchase_price' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($data, $request) {
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create([
                'category_id' => $data['category_id'] ?? null,
                'sku' => $data['sku'],
                'name' => $data['name'],
                'image' => $data['image'] ?? null,
                'purchase_price' => $data['purchase_price'],
                'price' => $data['price'],
                'min_stock' => $data['min_stock'],
                'unit' => $data['unit'],
                'stock' => collect($data['variants'])->sum('stock'), // cache total
            ]);

            foreach ($data['variants'] as $i => $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color' => $variant['color'] ?? null,
                    'size' => $variant['size'] ?? null,
                    'sku_variant' => $data['sku'].'-'.($i + 1),
                    'stock' => $variant['stock'],
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Barang & varian berhasil ditambahkan.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('variants');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'purchase_price' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:20',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($data, $request, $product) {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update([
                'category_id' => $data['category_id'] ?? null,
                'sku' => $data['sku'],
                'name' => $data['name'],
                'image' => $data['image'] ?? $product->image,
                'purchase_price' => $data['purchase_price'],
                'price' => $data['price'],
                'min_stock' => $data['min_stock'],
                'unit' => $data['unit'],
            ]);

            $keepIds = [];

            foreach ($data['variants'] as $i => $variant) {
                if (! empty($variant['id'])) {
                    // update varian yang sudah ada
                    $existing = ProductVariant::find($variant['id']);
                    $existing->update([
                        'color' => $variant['color'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'stock' => $variant['stock'],
                    ]);
                    $keepIds[] = $existing->id;
                } else {
                    // varian baru
                    $new = ProductVariant::create([
                        'product_id' => $product->id,
                        'color' => $variant['color'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'sku_variant' => $product->sku.'-'.($i + 1).'-'.uniqid(),
                        'stock' => $variant['stock'],
                    ]);
                    $keepIds[] = $new->id;
                }
            }

            // hapus varian yang nggak ada lagi di form (user hapus baris pas edit)
            ProductVariant::where('product_id', $product->id)
                ->whereNotIn('id', $keepIds)
                ->delete();

            $product->update(['stock' => $product->fresh()->variants->sum('stock')]);
        });

        return redirect()->route('products.index')->with('success', 'Barang & varian berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete(); // varian ikut kehapus otomatis (cascadeOnDelete di migration)

        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
