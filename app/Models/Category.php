<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description'])]
class Category extends Model
{
    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }

    public function publishedPlaces(): HasMany
    {
        return $this->hasMany(Place::class)->where('is_published', true);
    }
}
