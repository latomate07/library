<?php

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->user = User::factory()->create();

    // Create permissions
    $permissions = [
        'view authors',
        'create authors',
        'update authors',
        'delete authors',
        'view books',
        'create books',
        'update books',
        'delete books',
        'manage library',
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }
});

test('guest can view books index without permission', function () {
    $response = $this->actingAs($this->user)
        ->get(route('books.index'));

    $response->assertStatus(200);
});

test('user can view books index with permission', function () {
    $this->user->givePermissionTo('view books');
    Book::factory()->count(3)->create();

    $response = $this->actingAs($this->user)
        ->get(route('books.index'));

    $response->assertStatus(200);
});

test('user can create book with permission', function () {
    $this->user->givePermissionTo('create books');
    $author = Author::factory()->create();

    $bookData = [
        'title' => 'Test Book',
        'price' => 29.99,
        'publication_date' => now(),
        'language' => 'en',
        'author_id' => $author->id,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('books.store'), $bookData);

    $response->assertRedirect(route('books.index'));
    $this->assertDatabaseHas('books', $bookData);
});

test('user cannot create book without permission', function () {
    $author = Author::factory()->create();

    $bookData = [
        'title' => 'Test Book',
        'price' => 29.99,
        'publication_date' => '2024-01-01',
        'language' => 'en',
        'author_id' => $author->id,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('books.store'), $bookData);

    $response->assertStatus(403);
});

test('book creation validates required fields', function () {
    $this->user->givePermissionTo('create books');

    $response = $this->actingAs($this->user)
        ->post(route('books.store'), []);

    $response->assertSessionHasErrors(['title', 'price', 'publication_date', 'language', 'author_id']);
});

test('book creation validates author exists', function () {
    $this->user->givePermissionTo('create books');

    $bookData = [
        'title' => 'Test Book',
        'price' => 29.99,
        'publication_date' => '2024-01-01',
        'language' => 'en',
        'author_id' => 999, // Non-existent author
    ];

    $response = $this->actingAs($this->user)
        ->post(route('books.store'), $bookData);

    $response->assertSessionHasErrors(['author_id']);
});

test('user can update book with permission', function () {
    $this->user->givePermissionTo('update books');
    $author = Author::factory()->create();
    $book = Book::factory()->create(['author_id' => $author->id]);

    $updateData = [
        'title' => 'Updated Book Title',
        'price' => 39.99,
        'publication_date' => now(),
        'language' => 'fr',
        'author_id' => $author->id,
    ];

    $response = $this->actingAs($this->user)
        ->put(route('books.update', $book), $updateData);

    $response->assertRedirect(route('books.index'));
    $this->assertDatabaseHas('books', $updateData);
});

test('user can delete book with permission', function () {
    $this->user->givePermissionTo('delete books');
    $book = Book::factory()->create();

    $response = $this->actingAs($this->user)
        ->delete(route('books.destroy', $book));

    $response->assertRedirect(route('books.index'));
    $this->assertModelMissing($book);
});

test('isbn must be unique when provided', function () {
    $this->user->givePermissionTo('create books');
    $author = Author::factory()->create();

    // Create first book with ISBN
    Book::factory()->create(['isbn' => '9781234567890']);

    $bookData = [
        'title' => 'Test Book',
        'isbn' => '9781234567890', // Duplicate ISBN
        'price' => 29.99,
        'publication_date' => '2024-01-01',
        'language' => 'en',
        'author_id' => $author->id,
    ];

    $response = $this->actingAs($this->user)
        ->post(route('books.store'), $bookData);

    $response->assertSessionHasErrors(['isbn']);
});