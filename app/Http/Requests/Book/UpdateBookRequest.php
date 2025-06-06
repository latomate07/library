<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update books');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'isbn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($this->book?->id),
            ],
            'price' => 'required|numeric|min:0|max:9999.99',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:2000',
            'pages' => 'nullable|integer|min:1|max:10000',
            'language' => 'required|string|max:10',
            'author_id' => 'required|exists:authors,id',
        ];
    }
}
