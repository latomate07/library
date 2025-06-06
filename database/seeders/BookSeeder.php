<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $authors = Author::whereIn('last_name', ['Hugo', 'Proust', 'Camus'])->get()->keyBy('last_name');

        $books = [
            'Hugo' => [
                [
                    'title' => 'Les Misérables',
                    'isbn' => '9782070409228',
                    'price' => 15.99,
                    'publication_date' => '1862-04-03',
                    'description' => 'Roman historique français qui décrit la vie de misérables dans Paris et la France provinciale du XIXe siècle.',
                    'pages' => 1488,
                    'language' => 'fr',
                ],
                [
                    'title' => 'Notre-Dame de Paris',
                    'isbn' => '9782070411313',
                    'price' => 13.99,
                    'publication_date' => '1831-03-16',
                    'description' => 'Roman historique français qui se déroule à Paris au XVe siècle.',
                    'pages' => 672,
                    'language' => 'fr',
                ],
            ],
            'Proust' => [
                [
                    'title' => 'Du côté de chez Swann',
                    'isbn' => '9782070754205',
                    'price' => 18.99,
                    'publication_date' => '1913-11-14',
                    'description' => 'Premier tome d\'À la recherche du temps perdu.',
                    'pages' => 544,
                    'language' => 'fr',
                ],
            ],
            'Camus' => [
                [
                    'title' => 'L\'Étranger',
                    'isbn' => '9782070360024',
                    'price' => 12.99,
                    'publication_date' => '1942-05-19',
                    'description' => 'Roman qui explore l\'absurdité de la condition humaine.',
                    'pages' => 192,
                    'language' => 'fr',
                ],
            ],
        ];

        foreach ($books as $authorLastName => $authorBooks) {
            if ($author = $authors->get($authorLastName)) {
                foreach ($authorBooks as $bookData) {
                    Book::create(array_merge($bookData, ['author_id' => $author->id]));
                }
            }
        }

        // Create additional random books
        Book::factory()->count(25)->create();
    }
}