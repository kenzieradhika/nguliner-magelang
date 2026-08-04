<?php

namespace App\Console\Commands;

use App\Models\IgPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportFeed extends Command
{
    protected $signature = 'feed:import {file?}';

    protected $description = 'Import post Instagram dari file JSON hasil scraper Python';

    public function handle(): int
    {
        $file = $this->argument('file') ?? 'data/instagram.json';

        $raw = is_file($file)
            ? file_get_contents($file)
            : Storage::disk('local')->get($file);

        if (! $raw) {
            $this->error("File tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $posts = json_decode($raw, true);

        if (! is_array($posts)) {
            $this->error('JSON tidak valid.');

            return self::FAILURE;
        }

        $imported = 0;

        foreach ($posts as $post) {
            $igId = $post['ig_id'] ?? null;

            if (! $igId || IgPost::where('ig_id', $igId)->exists()) {
                continue;
            }

            IgPost::create([
                'ig_id' => $igId,
                'image_url' => $post['image_url'] ?? null,
                'permalink' => $post['permalink'] ?? null,
                'caption' => $post['caption'] ?? null,
                'posted_at' => $post['posted_at'] ?? null,
            ]);

            $imported++;
        }

        $this->info("Selesai. {$imported} post diimpor.");

        return self::SUCCESS;
    }
}
