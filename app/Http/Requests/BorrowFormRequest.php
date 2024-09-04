<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;
use App\Models\Book;

class BorrowFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        return auth()->check();
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
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'borrowed_at' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(),
            'due_date' => 'nullable|date|after:borrowed_at'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }


    /**
     * Custom attribute names.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'book_id' => 'معرف الكتاب',
            'borrowed_at' => 'تاريخ الاستعارة',
            'due_date' => 'تاريخ الإرجاع المتوقع',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'book_id.required' => 'الحقل :attribute مطلوب.',
            'book_id.exists' => 'الكتاب المحدد غير موجود.',
            'borrowed_at.required' => 'الحقل :attribute مطلوب.',
            'borrowed_at.date' => 'الحقل :attribute يجب أن يكون تاريخًا صالحًا.',
            'borrowed_at.after_or_equal' => 'الحقل :attribute يجب أن يكون اليوم أو تاريخ لاحق.',
            'due_date.date' => 'الحقل :attribute يجب أن يكون تاريخًا صالحًا.',
            'due_date.after' => 'الحقل :attribute يجب أن يكون بعد تاريخ الاستعارة.',
        ];
    }
}
