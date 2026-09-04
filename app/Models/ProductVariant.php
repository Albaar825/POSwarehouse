<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'size',
        'sku_variant',
        'stock',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    /**
     * Relasi ke product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Semua gambar variant
     */
    public function images(): HasMany
    {
        return $this->hasMany(
            ProductVariantImage::class,
            'product_variant_id'
        )->orderBy('sort_order');
    }

    /**
     * Gambar utama variant
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(
            ProductVariantImage::class,
            'product_variant_id'
        )->where('is_primary', true);
    }

    /**
     * Label variant
     *
     * Contoh:
     * Hitam / M
     * Putih / L
     */
    public function label(): string
    {
        return collect([
            $this->color,
            $this->size,
        ])
            ->filter(fn ($value) => filled($value))
            ->implode(' / ');
    }
}
