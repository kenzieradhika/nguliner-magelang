<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'address', 'description', 'contact', 'status'])]
class PlaceSuggestion extends Model
{
    public const STATUSES = ['new', 'reviewed', 'imported'];
}
