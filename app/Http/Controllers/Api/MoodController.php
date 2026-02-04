<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mood;
use App\Models\Title;
use Illuminate\Http\Request;

class MoodController extends Controller
{
    // GET /api/moods
    public function index()
    {
        return response()->json(
            Mood::orderBy('name')->get()
        );
    }

    // GET /api/mood/{slug}?type=all&sort=popular&page=1
    public function show(string $slug, Request $request)
    {
        $mood = Mood::where('slug', $slug)->firstOrFail();

        $q = Title::query()
            ->with(['genres', 'moods'])
            ->whereHas('moods', fn($x) => $x->where('moods.id', $mood->id));

        if ($request->filled('type') && in_array($request->type, ['movie', 'series'], true)) {
            $q->where('type', $request->type);
        }

        $sort = $request->get('sort', 'popular');
        if ($sort === 'newest') {
            $q->orderBy('created_at', 'desc');
        } else {
            $q->orderBy('popularity', 'desc');
        }

        return response()->json([
            'mood' => $mood,
            'results' => $q->paginate(12),
        ]);
    }
}
