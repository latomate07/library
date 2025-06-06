<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Author;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'isbn',
        'price',
        'publication_date',
        'description',
        'pages',
        'language',
        'author_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'price' => 'decimal:2',
            'pages' => 'integer',
        ];
    }

    /**
     * Get the author of the book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Get the full title of the book.
     *
     * @return string
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%")
                ->orWhereHas('author', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Scope a query to filter books by author.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $authorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAuthor(Builder $query, int $authorId): Builder
    {
        return $query->where('author_id', $authorId);
    }
}
