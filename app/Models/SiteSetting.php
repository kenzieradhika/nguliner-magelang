<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'site_name', 'tagline', 'whatsapp', 'email', 'instagram', 'instagram_url',
    'meta_description', 'hero_eyebrow', 'hero_title', 'hero_title_italic',
    'hero_subtitle', 'address', 'copyright',
])]
class SiteSetting extends Model
{
    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }

    public static function defaults(): array
    {
        return [
            'site_name' => 'NGuliner',
            'tagline' => 'Referensi Kuliner No.1 di Magelang',
            'whatsapp' => null,
            'email' => null,
            'instagram' => '@ngulinermagelang',
            'instagram_url' => 'https://www.instagram.com/ngulinermagelang/',
            'meta_description' => 'Referensi kuliner Magelang: bakso, es dawet, martabak, nasi goreng magelangan, street food. Rekomendasi makan Magelang terpercaya.',
            'hero_eyebrow' => 'Kuliner Magelang & Sekitarnya',
            'hero_title' => 'Referensi Kuliner',
            'hero_title_italic' => 'No.1 di Magelang',
            'hero_subtitle' => 'Rekomendasi makan Magelang: bakso legendaris, es dawet, martabak, nasi goreng magelangan, hingga street food yang layak dicoba.',
            'address' => null,
            'copyright' => 'NGuliner Magelang',
        ];
    }
}
