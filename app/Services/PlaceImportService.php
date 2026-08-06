<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Support\Str;

class PlaceImportService
{
    /**
     * @return array{imported: int, skipped: int, updated: int}
     */
    public function import(array $items): array
    {
        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));

            if ($name === '') {
                $skipped++;
                continue;
            }

            $slug = Str::slug($name);
            $categorySlug = Str::slug($item['category'] ?? 'street-food');
            $category = Category::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $item['category'] ?? 'Street Food'],
            );

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
                'image' => $item['image'] ?? null,
                'is_published' => true,
            ];

            $existing = Place::query()->where('slug', $slug)->withTrashed()->first();

            if ($existing) {
                $existing->restore();
                $existing->update($data);
                $updated++;
            } else {
                Place::create($data);
                $imported++;
            }
        }

        return ['imported' => $imported, 'updated' => $updated, 'skipped' => $skipped];
    }
}