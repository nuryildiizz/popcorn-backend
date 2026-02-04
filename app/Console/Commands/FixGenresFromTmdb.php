<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Title;
use App\Models\Genre;
use App\Services\Tmdb;
use Illuminate\Support\Str;

class FixGenresFromTmdb extends Command
{
    protected $signature = 'popcorn:fix-genres {--limit=0}';
    protected $description = 'Re-fetch TMDB data and attach genres to existing titles';

    public function __construct(private Tmdb $tmdb)
    {
        parent::__construct();
    }

    public function handle()
    {
        $limit = (int) $this->option('limit');

        $q = Title::whereNotNull('tmdb_id');

        if ($limit > 0) {
            $q->limit($limit);
        }

        $titles = $q->get();
        $count = $titles->count();

        if (!$count) {
            $this->info('No titles found.');
            return Command::SUCCESS;
        }

        $this->info("Processing {$count} titles...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($titles as $title) {
            try {
                $data = $this->tmdb->movieDetails($title->tmdb_id);

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
            } catch (\Throwable $e) {
                $this->error("Failed: {$title->id} ({$title->tmdb_id})");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');
        return Command::SUCCESS;
    }
}
