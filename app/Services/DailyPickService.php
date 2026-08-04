<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Support\Collection;

class DailyPickService
{
    public function pick(int $limit = 1): Collection
    {
        $candidates = Place::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->with('category')
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        $seed = (int) now()->format('Ymd');

        return $candidates
            ->sortBy(fn (Place $place) => $this->hash($place->id, $seed))
            ->take($limit)
            ->values();
    }

    private function hash(int $id, int $seed): int
    {
        return crc32("{$seed}:{$id}");
    }
}
