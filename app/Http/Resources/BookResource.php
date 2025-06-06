<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'price' => $this->price,
            'publication_date' => $this->publication_date->format('Y-m-d'),
            'description' => $this->description,
            'pages' => $this->pages,
            'language' => $this->language,
            'author_id' => $this->author_id,
            'author' => new AuthorResource($this->whenLoaded('author')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
