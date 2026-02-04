<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Title;
use App\Models\Mood;

class FixMoodsFromGenres extends Command
{
    protected $signature = 'popcorn:fix-moods {--limit=0}';
    protected $description = 'Attach moods to titles based on their genres';

    protected array $map = [
        'horror' => 'korkutucu',
        'comedy' => 'guldursun',
        'action' => 'aksiyon-dolu',
        'thriller' => 'adrenalin-yukseltsin',
        'romance' => 'sevgili-ile-izlemelik',
        'drama' => 'dusundursun',
        'family' => 'aileyle-izlemelik',
        'animation' => 'cocuk',
        'documentary' => 'belgesel-meraklisi',
        'history' => 'tarihi-yolculuk',
        'fantasy' => 'hayalperest',
        'science-fiction' => 'uzaya-cikmalik',
        'mystery' => 'tek-basina',
        'war' => 'tarihi-yolculuk',
        'crime' => 'kaos-modu',
        'adventure' => 'haftasonu-keyfi',
    ];

    public function handle()
    {
        $limit = (int) $this->option('limit');

        $q = Title::with('genres')->whereNotNull('tmdb_id');
        if ($limit > 0) $q->limit($limit);

        $titles = $q->get();

        foreach ($titles as $title) {
            $moodIds = [];

            foreach ($title->genres as $genre) {
                $g = $genre->slug;
                if (!isset($this->map[$g])) continue;

                $mood = Mood::where('slug', $this->map[$g])->first();
                if ($mood) $moodIds[] = $mood->id;
            }

            if ($moodIds) {
                $title->moods()->syncWithoutDetaching($moodIds);
            }
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }
}
