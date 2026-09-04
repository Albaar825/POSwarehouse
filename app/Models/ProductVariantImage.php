<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'image',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relasi ke product variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    /**
     * URL gambar
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}
