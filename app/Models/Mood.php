<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    protected $fillable = ['name', 'slug'];

    public function titles()
    {
        return $this->belongsToMany(Title::class);
    }
}
