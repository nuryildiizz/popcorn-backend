<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Title;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
    'settings' => 'array',
];


    
    public function favorites()
    {
        return $this->belongsToMany(
            Title::class,
            'favorites',
            'user_id',
            'title_id'
        )->withTimestamps();
    }

    
    public function watchlist()
    {
        return $this->belongsToMany(
            Title::class,
            'watchlists',
            'user_id',
            'title_id'
        )->withTimestamps();
    }

    public function watched()
{
    return $this->belongsToMany(
        Title::class,
        'watches',   
        'user_id',
        'title_id'
    )
    ->withTimestamps()
    ->withPivot('watched_at');
}

}
