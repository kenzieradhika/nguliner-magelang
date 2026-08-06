<?php

namespace Database\Factories;

use App\Models\Microsite;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Microsite>
 */
class MicrositeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'place_id' => Place::factory(),
            'hero_title' => fake()->sentence(3),
            'about' => fake()->paragraph(),
            'menu' => [
                ['name' => fake()->word(), 'price' => 'Rp 15.000'],
            ],
            'gallery' => [],
            'socials' => [],
            'is_active' => true,
        ];
    }
}