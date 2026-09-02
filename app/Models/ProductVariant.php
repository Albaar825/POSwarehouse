<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'color',
        'size',
        'sku_variant',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function label(): string
    {
        return trim(($this->color ?? '-') . ' / ' . ($this->size ?? '-'), ' /');
    }
}
