<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Models\Genre;
use App\Models\Mood;

class HomeController extends Controller
{
    public function index()
    {
        return response()->json([
            'featuredMovies' => Title::where('type', 'movie')
                ->whereNotNull('featured_rank')
                ->orderBy('featured_rank')
                ->with('genres')
                ->take(6)
                ->get(),

            'featuredSeries' => Title::where('type', 'series')
                ->whereNotNull('featured_rank')
                ->orderBy('featured_rank')
                ->with('genres')
                ->take(6)
                ->get(),

            'categories' => Genre::all(),
            'moods' => Mood::all(),
        ]);
    }
}
