<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportPlaces extends Command
{
    protected $signature = 'places:import {file?} {--json}';

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

        $imported = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $name = trim($item['name'] ?? '');

            if (! $name) {
                $skipped++;
                continue;
            }

            $slug = Str::slug($name);
            $categorySlug = Str::slug($item['category'] ?? 'street-food');
            $category = Category::firstOrCreate(['slug' => $categorySlug], ['name' => $item['category'] ?? 'Street Food']);

            $data = [
                'category_id' => $category->id,
                'name' => $name,
                'slug' => $slug,
                'tagline' => $item['tagline'] ?? null,
                'description' => $item['description'] ?? null,
                'address' => $item['address'] ?? null,
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
                'whatsapp' => $item['whatsapp'] ?? null,
                'open_days' => $item['open_days'] ?? null,
                'open_time' => $item['open_time'] ?? null,
                'close_time' => $item['close_time'] ?? null,
                'price_range' => $item['price_range'] ?? null,
                'tips' => $item['tips'] ?? null,
                'since_year' => $item['since_year'] ?? null,
                'is_legendary' => (bool) ($item['is_legendary'] ?? false),
                'is_featured' => (bool) ($item['is_featured'] ?? false),
                'image' => $item['image'] ?? '/img/places/street.svg',
                'is_published' => true,
            ];

            $existing = Place::where('slug', $slug)->first();

            if ($existing) {
                $existing->update($data);
                $this->line("Diperbarui: {$name}");
            } else {
                Place::create($data);
                $this->line("Ditambahkan: {$name}");
            }

            $imported++;
        }

        $this->info("Selesai. {$imported} diimpor, {$skipped} dilewati.");

        return self::SUCCESS;
    }
}
