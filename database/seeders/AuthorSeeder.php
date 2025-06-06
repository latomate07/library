<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        // Create some famous authors
        $authors = [
            [
            'first_name' => 'Victor',
            'last_name' => 'Hugo',
            'biography' => 'Poète, romancier et dramaturge français du mouvement romantique.',
            'birth_date' => '1802-02-26',
            'nationality' => 'Française',
            ],
            [
            'first_name' => 'Marcel',
            'last_name' => 'Proust',
            'biography' => 'Romancier, critique et essayiste français, célèbre pour son œuvre À la recherche du temps perdu.',
            'birth_date' => '1871-07-10',
            'nationality' => 'Française',
            ],
            [
            'first_name' => 'Albert',
            'last_name' => 'Camus',
            'biography' => 'Philosophe, auteur et journaliste français.',
            'birth_date' => '1913-11-07',
            'nationality' => 'Française',
            ],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }

        // Create additional random authors
        Author::factory()->count(10)->create();
    }
}