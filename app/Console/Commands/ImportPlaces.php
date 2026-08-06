<?php

namespace App\Console\Commands;

use App\Services\PlaceImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportPlaces extends Command
{
    protected $signature = 'places:import {file?}';

    protected $description = 'Import data kuliner dari file JSON hasil scraper Python';

    public function handle(): int
    {
        $file = $this->argument('file') ?? 'data/kuliner.json';

        $raw = is_file($file)
            ? file_get_contents($file)
            : Storage::disk('local')->get($file);

        if (! $raw) {
            $this->error("File tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $items = json_decode($raw, true);

        if (! is_array($items)) {
            $this->error('JSON tidak valid.');

            return self::FAILURE;
        }

        $result = app(PlaceImportService::class)->import($items);

        $this->info(
            "Selesai. {$result['imported']} ditambahkan, {$result['updated']} diperbarui, {$result['skipped']} dilewati."
        );

        return self::SUCCESS;
    }
}