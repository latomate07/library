<?php
namespace App\Http\Controllers;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function index(Request $request): Response
    {
        $books = Book::with('author')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->search($request->input('search'));
            })
            ->when($request->filled('author_id'), function ($query) use ($request) {
                $query->byAuthor($request->input('author_id'));
            })
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        $authors = Author::orderBy('last_name')
                        ->orderBy('first_name')
                        ->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Books/Index', [
            'books' => $books,
            'authors' => $authors,
            'filters' => $request->only(['search', 'author_id']),
        ]);
    }

    public function create(): Response
    {
        $authors = Author::orderBy('last_name')
                        ->orderBy('first_name')
                        ->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Books/Create', [
            'authors' => $authors,
        ]);
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        Book::create($request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book created successfully.');
    }

    public function show(Book $book): Response
    {
        $book->load('author');

        return Inertia::render('Books/Show', [
            'book' => $book,
        ]);
    }

    public function edit(Book $book): Response
    {
        $authors = Author::orderBy('last_name')
                        ->orderBy('first_name')
                        ->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Books/Edit', [
            'book' => $book,
            'authors' => $authors,
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        if(!$book) {
            return redirect()->route('books.index')
                ->with('error', 'Book not found.');
        }

        $book->update($request->validated());

        return redirect()->route('books.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully.');
    }
}