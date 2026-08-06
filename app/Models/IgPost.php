<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['ig_id', 'image_url', 'permalink', 'caption', 'posted_at'])]
class IgPost extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
        ];
    }
}
