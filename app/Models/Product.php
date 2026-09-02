<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'image',
        'purchase_price',
        'price',
        'stock',
        'min_stock',
        'unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function totalStock(): int
    {
        return $this->variants->sum('stock');
    }

    public function isLowStock(): bool
    {
        return $this->totalStock() <= $this->min_stock;
    }

    public function imageUrl(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/no-image.png');
    }
}
