<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->optional($weight = 0.85)->name(),
            'date_of_birth' => fake()->optional($weight = 0.85)->date(),
            'status' => fake()->optional($weight = 0.85)->realText($maxNbChars = 30, $indexSize = 2),
            'location' => fake()->optional($weight = 0.85)->country(),
            'user_id' => fake()->unique()->numberBetween(3,\App\Models\User::Get()->count()),
        ];
    }
}
