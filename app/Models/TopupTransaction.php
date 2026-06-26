<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopupTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'coin_amount',
        'status',
        'signature',
        'qris_url',
        'paid_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'amount' => 'float',
        'coin_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
