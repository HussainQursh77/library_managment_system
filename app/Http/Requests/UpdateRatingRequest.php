<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRatingRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assuming all authenticated users can update their ratings
    }

    public function rules()
    {
        return [
            'rating' => 'nullable|integer|between:1,5',
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
