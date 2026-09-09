<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Transaction;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'type',
        'quantity',
        'source',
        'transaction_id',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Produk utama
     */
    public function product()
    {
        return $this->belongsTo(
            Product::class,
            'product_id'
        );
    }

    /**
     * Variant produk
     */
    public function productVariant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    /**
     * User yang melakukan perubahan stock
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Transaction terkait stock movement
     */
    public function transaction()
    {
        return $this->belongsTo(
            Transaction::class,
            'transaction_id'
        );
    }
}
