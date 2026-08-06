<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'category_id', 'name', 'slug', 'tagline', 'description', 'address',
    'latitude', 'longitude', 'whatsapp', 'open_days', 'open_time', 'close_time',
    'price_range', 'tips', 'since_year', 'is_legendary', 'is_featured',
    'image', 'views', 'is_published', 'publish_at',
])]
class Place extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_legendary' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'views' => 'integer',
            'publish_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function microsite(): HasOne
    {
        return $this->hasOne(Microsite::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function averageRating(): float
    {
        $reviews = $this->approvedReviews;
        if ($reviews->isEmpty()) {
            return 0;
        }

        return round($reviews->avg('rating'), 1);
    }

    public function reviewCount(): int
    {
        return $this->approvedReviews->count();
    }

    public function openDayKeys(): array
    {
        return collect(explode(',', (string) $this->open_days))
            ->map(fn ($d) => strtolower(trim($d)))
            ->filter()
            ->values()
            ->toArray();
    }

    public function isOpenNow(?Carbon $now = null): bool
    {
        $now ??= now();

        if (! $this->open_days || ! $this->open_time) {
            return false;
        }

        $today = strtolower($now->format('D'));

        if (! in_array($today, $this->openDayKeys(), true)) {
            return false;
        }

        $open = strtotime($this->open_time);
        $close = $this->close_time ? strtotime($this->close_time) : null;
        $current = $now->getTimestamp();

        if ($close === null || $close < $open) {
            return $current >= $open;
        }

        return $current >= $open && $current <= $close;
    }

    public function openStatusText(): string
    {
        if (! $this->open_days || ! $this->open_time) {
            return 'Jadwal tidak diketahui';
        }

        $close = $this->close_time ?: 'habis';

        return $this->isOpenNow() ? 'Buka' : "Tutup ({$this->open_time} - {$close})";
    }

    public function ageLabel(): string
    {
        if (! $this->since_year) {
            return '';
        }

        $years = now()->year - $this->since_year;

        return "Sejak {$this->since_year} ({$years}+ tahun)";
    }
}
