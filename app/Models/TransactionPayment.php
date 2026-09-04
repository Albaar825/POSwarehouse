<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'amount',
        'payment_method',
        'note',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
