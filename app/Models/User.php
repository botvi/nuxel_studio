<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_jalur',
        'kuansing_poin',
        'email',
        'password',
        'role',
        'google_id',
        'foto_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            $user->modelJalur()->create([
                'model_jalur' => [
                    'customColors' => [
                        'boat' => '#8D6E63',
                        'hair' => '#111827',
                        'pants' => '#38a169',
                        'shirt' => '#10B981',
                        'paddle' => '#8D6E63',
                        'splash' => '#a5f3fc',
                    ],
                    'boat_unlocked' => false,
                    'corak_data_url' => null,
                    'lambai_data_url' => null,
                    'lambai_unlocked' => false,
                ],
                'fitur_corak' => 'inactive',
                'fitur_lambai' => 'inactive',
            ]);
        });
    }

    public function modelJalur()
    {
        return $this->hasOne(ModelJalur::class);
    }

    public function wins()
    {
        return $this->hasMany(Room::class, 'winner_id');
    }

    public function losses()
    {
        return $this->hasMany(Room::class, 'loser_id');
    }
}
