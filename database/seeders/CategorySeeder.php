<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bakso', 'slug' => 'bakso', 'description' => 'Bakso legendaris dan kekinian se-Magelang'],
            ['name' => 'Es Dawet', 'slug' => 'es-dawet', 'description' => 'Dawet asli Magelang, gula Jawa & santan gurih'],
            ['name' => 'Martabak', 'slug' => 'martabak', 'description' => 'Martabak manis & martabak telur favorit'],
            ['name' => 'Nasi Goreng Magelangan', 'slug' => 'nasi-goreng-magelangan', 'description' => 'Nasi goreng khas Magelangan yang gurih kaya'],
            ['name' => 'Kopi & Wedang', 'slug' => 'kopi-wedang', 'description' => 'Kopi jos, wedang ronde, dan minuman tradisional'],
            ['name' => 'Street Food', 'slug' => 'street-food', 'description' => 'Jajanan kaki lima & street food Magelang'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
