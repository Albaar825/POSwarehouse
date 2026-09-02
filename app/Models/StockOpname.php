<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'user_id',
        'opname_date',
        'status',
        'note',
    ];

    protected $casts = [
        'opname_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }
}
