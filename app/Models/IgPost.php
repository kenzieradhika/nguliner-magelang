<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ig_id', 'image_url', 'permalink', 'caption', 'posted_at'])]
class IgPost extends Model
{
    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
        ];
    }
}
