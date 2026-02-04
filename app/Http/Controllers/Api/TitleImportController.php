<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Models\Genre;
use App\Services\Tmdb;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TitleImportController extends Controller
{
    public function __construct(private Tmdb $tmdb) {}

    public function import(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer|unique:titles,tmdb_id',
        ]);

        $tmdbId = (int) $request->tmdb_id;

        $existing = Title::where('tmdb_id', $tmdbId)->first();
        if ($existing) {
            return response()->json($existing);
        }

        $data = $this->tmdb->movieDetails($tmdbId);

        $name = $data['title'] ?? $data['name'] ?? 'Unknown';
        $posterPath = $data['poster_path'] ?? null;

        $title = Title::create([
            'tmdb_id' => $tmdbId,
            'type' => 'movie',

            'name' => $name,
            'slug' => Str::slug($name) . '-' . $tmdbId,

            'overview' => $data['overview'] ?? null,

            'poster_path' => $posterPath,
            'poster_url' => $posterPath
                ? 'https://image.tmdb.org/t/p/w500' . $posterPath
                : null,

            'release_date' => $data['release_date'] ?? null,
            'popularity' => $data['popularity'] ?? 0,
        ]);

        // ============================
        // GENRE BAĞLAMA (KRİTİK KISIM)
        // ============================
        $genreIds = [];

        foreach (($data['genres'] ?? []) as $g) {
            $slug = Str::slug($g['name']);

            $genre = Genre::firstOrCreate(
                ['slug' => $slug],
                ['name' => $g['name']]
            );

            $genreIds[] = $genre->id;
        }

        if (count($genreIds)) {
            $title->genres()->sync($genreIds);
        }
        // ============================

        return response()->json($title, 201);
    }
}
