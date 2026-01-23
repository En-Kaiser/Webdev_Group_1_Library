<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author_id' => ['required', 'exists:authors,author_id'],
            'genre_id' => ['required', 'exists:genres,genre_id'],
            'type' => ['required', 'in:physical,e_book'],
            'status' => ['required', 'in:available,unavailable'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'year' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'short_description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Book title is required.',
            'title.max' => 'Book title must not exceed 255 characters.',
            'author_id.required' => 'Author is required.',
            'author_id.exists' => 'Selected author does not exist.',
            'genre_id.required' => 'Genre is required.',
            'genre_id.exists' => 'Selected genre does not exist.',
            'type.required' => 'Book type is required.',
            'type.in' => 'Book type must be either physical or e-book.',
            'status.required' => 'Availability status is required.',
            'status.in' => 'Status must be either available or unavailable.',
            'cover_image.image' => 'Cover image must be a valid image file.',
            'cover_image.mimes' => 'Cover image must be jpeg, png, or jpg format.',
            'cover_image.max' => 'Cover image must not exceed 2MB.',
            'year.integer' => 'Year must be a valid number.',
            'year.min' => 'Year must be at least 1000.',
            'year.max' => 'Year cannot be in the future.',
        ];
    }
}
