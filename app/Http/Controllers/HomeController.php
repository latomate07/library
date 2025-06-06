<?php
namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        // Get all books with authors for public display
        $books = Book::with('author')
            ->orderBy('title')
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'isbn' => $book->isbn,
                    'price' => $book->price,
                    'publication_date' => $book->publication_date,
                    'description' => $book->description,
                    'pages' => $book->pages,
                    'language' => $book->language,
                    'author' => $book->author ? [
                        'id' => $book->author->id,
                        'full_name' => $book->author->full_name,
                    ] : null,
                ];
            });

        // Get all authors with their books for public display
        $authors = Author::withCount('books')
            ->with(['books' => function ($query) {
                $query->select('id', 'title', 'author_id', 'publication_date')
                      ->orderBy('publication_date', 'desc')
                      ->limit(5);
            }])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($author) {
                return [
                    'id' => $author->id,
                    'first_name' => $author->first_name,
                    'last_name' => $author->last_name,
                    'full_name' => $author->full_name,
                    'biography' => $author->biography,
                    'birth_date' => $author->birth_date,
                    'nationality' => $author->nationality,
                    'books_count' => $author->books_count,
                    'books' => $author->books->map(function ($book) {
                        return [
                            'id' => $book->id,
                            'title' => $book->title,
                        ];
                    }),
                ];
            });

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'books' => $books,
            'authors' => $authors,
        ]);
    }
}