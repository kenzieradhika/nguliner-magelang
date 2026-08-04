<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang NGuliner',
                'slug' => 'tentang',
                'meta_title' => 'Tentang NGuliner Magelang',
                'meta_description' => 'NGuliner Magelang adalah platform referensi kuliner Magelang: bakso, es dawet, martabak, nasi goreng magelangan, dan street food.',
                'sections' => [
                    ['type' => 'heading', 'content' => 'Tentang Kami'],
                    ['type' => 'text', 'content' => 'NGuliner Magelang adalah platform digital yang menyajikan rekomendasi kuliner terbaik di Magelang dan sekitarnya. Kami berfokus pada makanan legendaris, street food, hingga resto modern yang layak dicoba.'],
                    ['type' => 'heading', 'content' => 'Visi'],
                    ['type' => 'text', 'content' => 'Menjadi sumber terpercaya bagi siapa saja yang ingin menjelajahi kekayaan rasa Magelang — dari yang legendaris hingga yang baru muncul.'],
                    ['type' => 'list', 'items' => ['Rekomendasi harian yang berganti setiap hari', 'Kategori kuliner lengkap', 'Info praktis: alamat, jam buka, harga, tips', 'Support UMKM & resto lokal']],
                    ['type' => 'cta', 'text' => 'Mau kolaborasi?', 'url' => '/kolaborasi', 'button' => 'Hubungi Kami'],
                ],
                'is_published' => true,
            ],
            [
                'title' => 'Kerja Sama',
                'slug' => 'kerja-sama',
                'meta_title' => 'Kerja Sama & Kolaborasi NGuliner Magelang',
                'meta_description' => 'Kolaborasi bisnis, iklan, endorse, dan review resto/UMKM bersama NGuliner Magelang.',
                'sections' => [
                    ['type' => 'heading', 'content' => 'Kolaborasi Bersama NGuliner'],
                    ['type' => 'text', 'content' => 'Kami terbuka untuk kolaborasi bisnis, iklan, dan endorse. Jangkau pecinta kuliner Magelang melalui platform kami.'],
                    ['type' => 'list', 'items' => ['Iklan & Endorse', 'Review resto / UMKM', 'Partnership konten', 'Saran tempat baru']],
                    ['type' => 'text', 'content' => 'Hubungi kami via Instagram @ngulinermagelang atau isi form kolaborasi.'],
                    ['type' => 'cta', 'text' => 'Siap berkolaborasi?', 'url' => '/kolaborasi', 'button' => 'Isi Form Kolaborasi'],
                ],
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::firstOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
