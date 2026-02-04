<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Client\Response;


class Tmdb
{
    private function base(): string
    {
        return config('services.tmdb.base', 'https://api.themoviedb.org/3');
    }

    private function token(): string
    {
        return (string) config('services.tmdb.token');
    }

    public function get(string $path, array $query = [])
    {
        /** @var Response $res */
$res = Http::withToken($this->token())
    ->acceptJson()
    ->get($this->base() . $path, $query);


if (!$res->successful()) {
  abort($res->status(), $res->body());
}

return $res->json();

    }

  public function searchMovie(string $query, int $pages = 1): array
{
    $all = [];
    $page = 1;
    $totalPages = 1;

    while ($page <= $pages && $page <= $totalPages) {
        $data = $this->get('/search/movie', [
            'query' => $query,
            'page'  => $page,
        ]);

        $results = $data['results'] ?? [];
        $all = array_merge($all, $results);

        $totalPages = (int)($data['total_pages'] ?? 1);
        $page++;
    }

    return [
        'results' => $all,
        'pages_loaded' => min($pages, $totalPages),
        'total_pages' => $totalPages,
        'total_results' => (int)($data['total_results'] ?? count($all)),
    ];
}


    public function movieDetails(int $id, string $lang = 'tr-TR')
    {
        return $this->get("/movie/{$id}", [
            'language' => $lang,
        ]);
    }
}
