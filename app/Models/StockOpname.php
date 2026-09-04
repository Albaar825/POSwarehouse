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

    protected function casts(): array
    {
        return ['opname_date' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function totalDifference(): int
    {
        return $this->details->sum('difference');
    }
}
