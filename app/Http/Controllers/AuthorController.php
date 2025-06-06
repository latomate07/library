<?php

namespace App\Http\Controllers;

use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthorController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Author::withBooksCount();

        $query->when($request->filled('search'), function ($query) use ($request) {
            $query->search($request->input('search'));
        });

        $authors = $query->orderBy('last_name')
                        ->orderBy('first_name')
                        ->paginate(15)
                        ->withQueryString();

        return Inertia::render('Authors/Index', [
            'authors' => $authors,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Authors/Create');
    }

    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        Author::create($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Author created successfully.');
    }

    public function show(Author $author): Response
    {
        $author->load(['books' => function ($query) {
            $query->orderBy('publication_date', 'desc');
        }]);

        return Inertia::render('Authors/Show', [
            'author' => $author,
        ]);
    }

    public function edit(Author $author): Response
    {
        return Inertia::render('Authors/Edit', [
            'author' => $author,
        ]);
    }

    public function update(UpdateAuthorRequest $request, Author $author): RedirectResponse
    {
        $author->update($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Author updated successfully.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Cannot delete author with existing books.');
        }

        $author->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Author deleted successfully.');
    }
}