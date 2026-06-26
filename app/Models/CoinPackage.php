<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coin_amount',
        'price',
        'description',
    ];

    protected $casts = [
        'coin_amount' => 'integer',
        'price' => 'float',
    ];
}
