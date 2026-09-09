<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'type',
        'user_id',
        'customer_id',
        'total',
        'paid',
        'change',
        'payment_method',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'total' => 'integer',
        'paid' => 'integer',
        'change' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function payments()
    {
        return $this->hasMany(TransactionPayment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getRemainingAttribute()
    {
        return max(
            0,
            $this->total - $this->paid
        );
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOnHold(): bool
    {
        return $this->status === 'on_hold';
    }

    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    public function isOpenInvoice(): bool
    {
        return $this->type === 'open_invoice'
            && $this->status === 'on_hold';
    }
}
