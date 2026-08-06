<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Place>
 */
class PlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->unique()->company(),
            'slug' => fn (array $attributes) => str()->slug($attributes['name']),
            'tagline' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'address' => fake()->streetAddress(),
            'latitude' => fake()->latitude(-7.7, -7.4),
            'longitude' => fake()->longitude(110.2, 110.4),
            'whatsapp' => fake()->numerify('08##########'),
            'open_days' => 'Senin - Minggu',
            'open_time' => '08:00',
            'close_time' => '21:00',
            'price_range' => 'Rp 10.000 - Rp 50.000',
            'since_year' => fake()->numberBetween(1990, 2024),
            'is_legendary' => false,
            'is_featured' => false,
            'views' => fake()->numberBetween(0, 5000),
            'is_published' => true,
        ];
    }
}