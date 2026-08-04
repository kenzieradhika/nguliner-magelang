<?php

namespace Database\Seeders;

use App\Models\IgPost;
use Illuminate\Database\Seeder;

class IgPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'ig_id' => 'ig-sample-01',
                'image_url' => '/img/ig-placeholder.svg',
                'permalink' => 'https://www.instagram.com/ngulinermagelang/',
                'caption' => 'Es Dawet Pak Min — legend sejak 2001. Santan gurih + gula Jawa asli. 📍 Jl. Kalingga, Magelang',
                'posted_at' => now()->subDays(1),
            ],
            [
                'ig_id' => 'ig-sample-02',
                'image_url' => '/img/ig-placeholder.svg',
                'permalink' => 'https://www.instagram.com/ngulinermagelang/',
                'caption' => 'Bakso Krikil Mas Kentung — sampai 60 butir semangkok! 🔥',
                'posted_at' => now()->subDays(2),
            ],
            [
                'ig_id' => 'ig-sample-03',
                'image_url' => '/img/ig-placeholder.svg',
                'permalink' => 'https://www.instagram.com/ngulinermagelang/',
                'caption' => 'Kopi Jos Pak Siswanto di Alun-alun Magelang. Klasik abadi ☕',
                'posted_at' => now()->subDays(3),
            ],
            [
                'ig_id' => 'ig-sample-04',
                'image_url' => '/img/ig-placeholder.svg',
                'permalink' => 'https://www.instagram.com/ngulinermagelang/',
                'caption' => 'Martabak Sahabat — legend sejak tahun 2000. Wajib mampir!',
                'posted_at' => now()->subDays(4),
            ],
            [
                'ig_id' => 'ig-sample-05',
                'image_url' => '/img/ig-placeholder.svg',
                'permalink' => 'https://www.instagram.com/ngulinermagelang/',
                'caption' => 'Nasi Goreng Magelangan Bu Lurah — gurih kaya isi 🍳',
                'posted_at' => now()->subDays(5),
            ],
            [
                'ig_id' => 'ig-sample-06',
                'image_url' => '/img/ig-placeholder.svg',
                'permalink' => 'https://www.instagram.com/ngulinermagelang/',
                'caption' => 'Support resto & UMKM lokal. Mau direview? DM kami! 💌',
                'posted_at' => now()->subDays(6),
            ],
        ];

        foreach ($posts as $post) {
            IgPost::firstOrCreate(['ig_id' => $post['ig_id']], $post);
        }
    }
}
