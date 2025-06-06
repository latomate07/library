<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'isbn' => fake()->optional(0.8)->isbn13(),
            'price' => fake()->randomFloat(2, 5, 99.99),
            'publication_date' => fake()->dateTimeBetween('-50 years', 'now')->format('Y-m-d'),
            'description' => fake()->optional(0.7)->paragraph(5),
            'pages' => fake()->optional(0.9)->numberBetween(50, 1000),
            'language' => fake()->randomElement(['en', 'fr', 'es', 'de']),
            'author_id' => Author::factory(),
        ];
    }
}
