<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Book;
class StoreRatingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {

        if ($this->filled('book_name') && !$this->filled('book_id')) {
            $book = Book::where('title', $this->input('book_name'))->first();
            if ($book) {
                $this->merge([
                    'book_id' => $book->id,
                ]);
            }
        }
    }
    public function rules()
    {
        return [
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
