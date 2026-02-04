<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $type = $request->get('type', 'all'); // all | movie | series

        if ($q === '') {
            return response()->json([
                'data' => [],
                'message' => 'Boş arama.'
            ]);
        }

        $query = Title::query()
            ->select(['id', 'name', 'slug', 'type', 'poster_url'])
            ->where('name', 'like', "%{$q}%");

        if (in_array($type, ['movie', 'series'], true)) {
            $query->where('type', $type);
        }

        // En popülerden başlasın
        $results = $query->orderBy('popularity', 'desc')
            ->limit(10)
            ->get();

        return response()->json($results);
    }
}
