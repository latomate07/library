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

    public function store(StoreAuthorRequest $request): RedirectResponse
    {
        Author::create($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'Auteur créé avec succès.');
    }

    public function update(UpdateAuthorRequest $request, Author $author): RedirectResponse
    {
        $author->update($request->validated());

        return redirect()->route('authors.index')
            ->with('success', 'L\'auteur a été mis à jour avec succès.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Impossible de supprimer un auteur avec des livres existants.');
        }

        $author->delete();

        return redirect()->route('authors.index')
            ->with('success', 'Auteur supprimé avec succès.');
    }
}