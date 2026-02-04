<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $q = Title::query()
            ->with(['genres', 'moods'])
            ->whereNotNull('tmdb_id');

        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        $genresParam = $request->get('genres', $request->get('genre', null));

        if (!empty($genresParam)) {
            $raw = array_filter(array_map('trim', explode(',', (string) $genresParam)));

            $norm = function ($s) {
                $s = mb_strtolower($s);
                $map = ['ı' => 'i', 'ğ' => 'g', 'ş' => 's', 'ö' => 'o', 'ü' => 'u', 'ç' => 'c'];
                $s = strtr($s, $map);
                $s = preg_replace('/\s+/', '-', $s);
                return $s;
            };

            $normalized = array_map($norm, $raw);

            $q->whereHas('genres', function ($x) use ($raw, $normalized) {
                $x->whereIn('slug', $normalized)
                  ->orWhereIn('name', $raw);
            });
        }

        if ($request->filled('moods')) {
            $raw = array_filter(array_map('trim', explode(',', (string) $request->moods)));

            $norm = function ($s) {
                $s = mb_strtolower($s);
                $map = ['ı' => 'i', 'ğ' => 'g', 'ş' => 's', 'ö' => 'o', 'ü' => 'u', 'ç' => 'c'];
                $s = strtr($s, $map);
                $s = preg_replace('/\s+/', '-', $s);
                return $s;
            };

            $normalized = array_map($norm, $raw);

            $q->whereHas('moods', function ($x) use ($raw, $normalized) {
                $x->whereIn('slug', $normalized)
                  ->orWhereIn('name', $raw);
            });
        }

        $sort = $request->get('sort', 'popular');
        if ($sort === 'new') $sort = 'newest';

        if ($sort === 'featured') {
            $q->orderByRaw('featured_rank IS NULL, featured_rank ASC');
        } elseif ($sort === 'newest') {
            $q->orderBy('created_at', 'desc');
        } else {
            $q->orderBy('popularity', 'desc');
        }

        $perPage = (int) $request->get('perPage', $request->get('limit', 12));
        $perPage = max(1, min($perPage, 50));

        return response()->json($q->paginate($perPage));
    }

    public function show(string $slug)
    {
        $title = Title::with(['genres', 'moods'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($title);
    }
}
