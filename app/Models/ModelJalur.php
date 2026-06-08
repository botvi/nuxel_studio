<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelJalur extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model_jalur',
        'fitur_corak',
        'fitur_lambai',
        'statistik_jalur',
    ];

    protected $casts = [
        'model_jalur' => 'array',
        'statistik_jalur' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
