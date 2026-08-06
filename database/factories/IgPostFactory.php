<?php

namespace Database\Factories;

use App\Models\IgPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IgPost>
 */
class IgPostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ig_id' => fake()->unique()->numerify('##############'),
            'image_url' => fake()->imageUrl(640, 640),
            'permalink' => fake()->url(),
            'caption' => fake()->sentence(),
            'posted_at' => fake()->dateTimeThisYear(),
        ];
    }
}