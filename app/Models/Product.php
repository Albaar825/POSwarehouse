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
        'variant_groups',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'variant_groups' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | STOCK
    |--------------------------------------------------------------------------
    */

    public function totalStock(): int
    {
        /*
        |--------------------------------------------------------------------------
        | Gunakan query langsung supaya tidak harus load semua variant.
        |--------------------------------------------------------------------------
        */

        return (int) $this->variants()->sum('stock');
    }

    public function syncTotalStock(): int
    {
        $total = $this->totalStock();

        $this->update([
            'stock' => $total,
        ]);

        return $total;
    }

    public function isLowStock(): bool
    {
        return $this->totalStock() <= $this->min_stock;
    }

    /*
    |--------------------------------------------------------------------------
    | VARIANT
    |--------------------------------------------------------------------------
    */

    public function hasVariants(): bool
    {
        return is_array($this->variant_groups)
            && count($this->variant_groups) > 0;
    }

    public function variantGroups(): array
    {
        return $this->variant_groups ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | IMAGE
    |--------------------------------------------------------------------------
    */

    public function imageUrl(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-image.png');
    }
}
