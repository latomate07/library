<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create books');
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
            'isbn' => 'nullable|string|unique:books,isbn|max:20',
            'price' => 'required|numeric|min:0|max:9999.99',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:2000',
            'pages' => 'nullable|integer|min:1|max:10000',
            'language' => 'required|string|max:10',
            'author_id' => 'required|exists:authors,id',
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du livre est requis.',
            'isbn.unique' => 'L\'ISBN doit être unique.',
            'price.required' => 'Le prix du livre est requis.',
            'publication_date.required' => 'La date de publication est requise.',
            'author_id.exists' => 'L\'auteur sélectionné n\'existe pas.',
        ];
    }
}
