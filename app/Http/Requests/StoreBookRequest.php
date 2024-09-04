<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\Category;
class StoreBookRequest extends FormRequest
{

    protected $stopOnFirstFailure = true;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_admin === 'admin';
    }
    protected function prepareForValidation()
    {
        if ($this->has('category_name') && !$this->has('category_id')) {
            $category = Category::where('category', $this->input('category_name'))->first();
            if ($category) {
                $this->merge([
                    'category_id' => $category->id,
                ]);
            }
        }
        $this->merge([
            'category_id' => $this->input('category_id'),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:8|regex:/^[^\'"\\\\]+$/',
            'author' => 'required|string|max:255|min:5|regex:/^[^\'"\\\\]+$/',
            'description' => 'required|string|regex:/^[^\'"\\\\]+$/',
            'published_at' => 'required|date|regex:/^[^\'"\\\\]+$/',
            'category_id' => 'required'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }

    public function attributes()
    {
        return [
            'title' => 'عنوان الكتاب',
            'author' => 'اسم الؤلف',
            'description' => 'وصف الكتاب',
            'published_at' => 'تاريخ نشر الكتاب',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'الحقل :attribute مطلوب .',
            'string' => 'الحقل :attribute ياخذ محارف',
            'max' => 'الحقل :attribute يحوي على الاكثر 255 محرف',
            'min' => 'الحق :attribute يجب ان يكون 5 على الاقل',
            'regex' => 'الحقل :attribute لا يحوي على محارف خاصة',
            'date' => 'الحق :attribute يجب ان يكون تاريخ',
        ];
    }
}
