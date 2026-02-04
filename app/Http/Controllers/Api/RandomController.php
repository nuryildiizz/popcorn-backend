<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\Request;

class RandomController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = Title::query()->with(['genres', 'moods']);

        // type=movie|series
        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        // genre=slug
        if ($request->filled('genre')) {
            $genreSlug = $request->genre;
            $q->whereHas('genres', fn ($x) => $x->where('slug', $genreSlug));
        }

        // mood=slug
        if ($request->filled('mood')) {
            $moodSlug = $request->mood;
            $q->whereHas('moods', fn ($x) => $x->where('slug', $moodSlug));
        }

        // Basit random: MySQL'de RAND()
        $item = $q->inRandomOrder()->first();

        if (!$item) {
            return response()->json([
                'message' => 'Bu filtrelerle içerik bulunamadı.'
            ], 404);
        }

        return response()->json($item);
    }
}
