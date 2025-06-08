<?php

use App\Models\Author;
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

    // Create permissions
    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }
});

test('user can view authors index with permission', function () {
    $this->user->givePermissionTo('view authors');
    Author::factory()->count(3)->create();

    $response = $this->actingAs($this->user)
        ->get(route('authors.index'));

    $response->assertStatus(200);
});

test('user cannot view authors index without permission', function () {
    $response = $this->actingAs($this->user)
        ->get(route('authors.index'));

    $response->assertStatus(403);
});

test('user can create author with permission', function () {
    $this->user->givePermissionTo('create authors');

    $authorData = [
        'first_name' => 'Tahirou',
        'last_name' => 'Test',
        'biography' => 'A great author',
    ];

    $response = $this->actingAs($this->user)
        ->post(route('authors.store'), $authorData);

    $response->assertRedirect(route('authors.index'));
    $this->assertDatabaseHas('authors', $authorData);
});

test('user cannot create author without permission', function () {
    $authorData = [
        'first_name' => 'Tahirou',
        'last_name' => 'Test',
    ];

    $response = $this->actingAs($this->user)
        ->post(route('authors.store'), $authorData);

    $response->assertStatus(403);
});

test('author creation validates required fields', function () {
    $this->user->givePermissionTo('create authors');

    $response = $this->actingAs($this->user)
        ->post(route('authors.store'), []);

    $response->assertSessionHasErrors(['first_name', 'last_name']);
});

test('user can update author with permission', function () {
    $this->user->givePermissionTo('update authors');
    $author = Author::factory()->create();

    $updateData = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ];

    $response = $this->actingAs($this->user)
        ->put(route('authors.update', $author), $updateData);

    $response->assertRedirect(route('authors.index'));
    $this->assertDatabaseHas('authors', $updateData);
});

test('user can delete author without books', function () {
    $this->user->givePermissionTo('delete authors');
    $author = Author::factory()->create();

    $response = $this->actingAs($this->user)
        ->delete(route('authors.destroy', $author));

    $response->assertRedirect(route('authors.index'));
    $this->assertModelMissing($author);
});