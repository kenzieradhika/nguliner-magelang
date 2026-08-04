<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'place_id', 'hero_title', 'hero_image', 'about', 'menu', 'gallery',
    'socials', 'map_embed', 'accent_color', 'cta_text', 'is_active',
])]
class Microsite extends Model
{
    protected function casts(): array
    {
        return [
            'menu' => 'array',
            'gallery' => 'array',
            'socials' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
