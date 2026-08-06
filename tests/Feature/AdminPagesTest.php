<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
        ]);
    }

    #[DataProvider('urlProvider')]
    public function test_admin_pages_render(string $url): void
    {
        $this->actingAs($this->admin())
            ->get($url)
            ->assertStatus(200);
    }

    #[DataProvider('seededUrlProvider')]
    public function test_admin_pages_render_with_rows(string $url): void
    {
        $this->seedRows();

        $this->actingAs($this->admin())
            ->get($url)
            ->assertStatus(200);
    }

    private function seedRows(): void
    {
        $category = \App\Models\Category::factory()->create();
        $place = \App\Models\Place::factory()->create(['category_id' => $category->id]);
        \App\Models\IgPost::factory()->create();
        \App\Models\Microsite::factory()->create(['place_id' => $place->id]);
        \App\Models\Review::factory()->create(['place_id' => $place->id]);
    }

    public static function seededUrlProvider(): array
    {
        return [
            'places' => ['/admin/kuliner'],
            'kategori' => ['/admin/kategori'],
            'halaman' => ['/admin/halaman'],
            'feed ig' => ['/admin/feed-instagram'],
            'microsite' => ['/admin/microsite'],
        ];
    }

    public static function urlProvider(): array
    {
        return [
            'dashboard' => ['/admin'],
            'places' => ['/admin/kuliner'],
            'kategori' => ['/admin/kategori'],
            'halaman' => ['/admin/halaman'],
            'feed ig' => ['/admin/feed-instagram'],
            'microsite' => ['/admin/microsite'],
            'review' => ['/admin/review'],
            'kolaborasi' => ['/admin/kolaborasi'],
            'saran' => ['/admin/saran-tempat'],
            'audit log' => ['/admin/audit-log'],
            'pengguna' => ['/admin/pengguna'],
            'backup' => ['/admin/backup'],
            'sessions' => ['/admin/sessions'],
        ];
    }
}