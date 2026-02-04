<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Title;
use App\Models\Genre;
use App\Models\Mood;
use Illuminate\Support\Str;

class PopcornSeeder extends Seeder
{
    public function run()
    {
        // GENRES
        $genres = [
            'Aksiyon', 'Komedi', 'Dram', 'Bilim Kurgu', 'Korku',
            'Romantik', 'Belgesel', 'Macera', 'Fantastik'
        ];

        $genreModels = [];
        foreach ($genres as $g) {
            $genreModels[] = Genre::create([
                'name' => $g,
                'slug' => Str::slug($g),
            ]);
        }

        // MOODS (senin listeden)
        $moods = [
            "Sevgili İle İzlemelik",
            "Arkadaş Ortamı",
            "Haftasonu Keyfi",
            "Düşündürsün",
            "Rahatlatıcı",
            "Ağlatsın",
            "Adrenalin Yükseltsin",
            "Korkutucu",
            "Güldürsün",
            "Aksiyon Dolu",
            "Tek Başına",
            "Hayalperest",
            "Felsefik Düşünceler"
        ];

        $moodModels = [];
        foreach ($moods as $m) {
            $moodModels[] = Mood::create([
                'name' => $m,
                'slug' => Str::slug($m),
            ]);
        }

        // TITLES (Film + Dizi karışık)
        for ($i = 1; $i <= 30; $i++) {
            $title = Title::create([
                'type' => $i % 2 === 0 ? 'movie' : 'series',
                'name' => "Popcorn Title $i",
                'slug' => Str::slug("Popcorn Title $i"),
                'overview' => "Bu Popcorn için örnek açıklama metni #$i",
                'poster_url' => "https://via.placeholder.com/300x450?text=Popcorn+$i",
                'popularity' => rand(1, 100),
                'featured_rank' => $i <= 6 ? $i : null,
            ]);

            // 1-2 genre bağla
            $title->genres()->attach(
                collect($genreModels)->random(rand(1, 2))->pluck('id')
            );

            // 1-2 mood bağla
            $title->moods()->attach(
                collect($moodModels)->random(rand(1, 2))->pluck('id')
            );
        }
    }
}
