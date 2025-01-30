<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'text' => ['required', 'string', 'min:1', 'max:1000'],
            'model' => ['required', 'string'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:1.4'],
            'prompt' => ['string'],
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
            'text.required' => 'Text is required',
            'text.string' => 'Text must be a string',
            'text.min' => 'Text must be at least 1 character',
            'text.max' => 'Text must be at most 1000 characters',
            'model.required' => 'Model is required',
            'model.string' => 'Model must be a string',
            'temperature.required' => 'Temperature is required',
            'temperature.numeric' => 'Temperature must be a number',
            'temperature.min' => 'Temperature must be at least 0',
            'temperature.max' => 'Temperature must be at most 1.4',
            'prompt.string' => 'Prompt must be a string',
        ];
    }
}
