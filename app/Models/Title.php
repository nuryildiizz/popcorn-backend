<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Title extends Model
{
    protected $fillable = [
        'tmdb_id',
        'type',
        'name',
        'overview',
        'poster_path',
        'poster_url',
        'release_date',
        'popularity',
        'featured_rank',
        'slug',
    ];

    protected static function booted()
    {
        static::creating(function ($title) {
            // slug gönderilmediyse otomatik üret
            if (empty($title->slug)) {
                $base = Str::slug($title->name ?? 'title');

                // tmdb_id varsa benzersiz yap (en temizi)
                $title->slug = $title->tmdb_id
                    ? "{$base}-{$title->tmdb_id}"
                    : $base;
            }
        });
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function moods()
    {
        return $this->belongsToMany(Mood::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
