<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
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
            'text' => ['required', 'string', 'max:1000'],
            'source_language' => ['required', 'string', 'max:2', 'different:target_language'],
            'target_language' => ['required', 'string', 'max:2', 'different:source_language'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.required' => 'The text field is required.',
            'text.string' => 'The text field must be a string.',
            'text.max' => 'The text field must not exceed 1000 characters.',
            'source_language.required' => 'The source language field is required.',
            'source_language.string' => 'The source language field must be a string.',
            'source_language.max' => 'The source language field must not exceed 2 characters.',
            'source_language.different' => 'The source language and target language must be different.',
            'target_language.required' => 'The target language field is required.',
            'target_language.string' => 'The target language field must be a string.',
            'target_language.max' => 'The target language field must not exceed 2 characters.',
            'target_language.different' => 'The target language and source language must be different.',
        ];
    }
}
