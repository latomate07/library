<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'biography' => fake()->optional(0.7)->paragraph(3),
            'birth_date' => ($date = fake()->optional(0.8)->dateTimeBetween('-80 years', '-20 years')) ? $date->format('Y-m-d') : null,
            'nationality' => fake()->optional(0.6)->country(),
        ];
    }
}
