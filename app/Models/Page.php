<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'slug', 'image', 'sections', 'meta_title', 'meta_description', 'is_published', 'publish_at'])]
class Page extends Model
{
    use SoftDeletes;
    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'is_published' => 'boolean',
            'publish_at' => 'datetime',
        ];
    }
}
