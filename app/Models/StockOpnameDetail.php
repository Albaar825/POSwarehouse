<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameDetail extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'product_variant_id',
        'system_stock',
        'physical_stock',
        'difference',
        'note',
    ];

    protected $casts = [
        'system_stock' => 'integer',
        'physical_stock' => 'integer',
        'difference' => 'integer',
    ];

    /**
     * Relasi ke Stock Opname
     */
    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(
            StockOpname::class,
            'stock_opname_id'
        );
    }

    /**
     * Relasi ke Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Product::class,
            'product_id'
        );
    }

    /**
     * Relasi ke Product Variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}
