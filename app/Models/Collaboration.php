<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'whatsapp', 'business_name', 'type', 'message', 'status'])]
class Collaboration extends Model
{
    public const TYPES = ['iklan', 'endorse', 'review', 'partnership'];

    public const STATUSES = ['new', 'contacted', 'done'];
}
