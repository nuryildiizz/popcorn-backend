<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Tmdb;
use Illuminate\Http\Request;

class TmdbController extends Controller
{
    public function __construct(private Tmdb $tmdb) {}

    public function search(Request $request)
{
    $q = trim((string) $request->query('q', ''));
    if ($q === '') return response()->json(['results' => []]);

    $pages = (int) $request->query('pages', 5);
    $pages = max(1, min($pages, 15)); 

    return response()->json($this->tmdb->searchMovie($q, $pages));
}

}
