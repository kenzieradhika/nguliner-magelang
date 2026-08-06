<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'category', 'address', 'description', 'contact', 'status'])]
class PlaceSuggestion extends Model
{
    use SoftDeletes;
    public const STATUSES = ['new', 'reviewed', 'imported'];
}
