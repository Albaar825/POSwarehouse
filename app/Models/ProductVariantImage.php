<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantImage extends Model
{
    protected $fillable = [
        'product_variant_id',
        'image',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    public function url(): string
    {
        return asset('storage/' . $this->image);
    }
}
