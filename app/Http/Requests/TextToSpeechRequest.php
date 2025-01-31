<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TextToSpeechRequest extends FormRequest
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
            'model' => ['required', 'string', 'in:tts-1,tts-1-hd'],
            'text' => ['required', 'string', 'min:3', 'max:3000'],
            'voice' => ['required', 'string', 'in:alloy,ash,coral,echo,fable,onyx,nova,sage,shimmer'],
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
            'model.required' => 'The model is required.',
            'model.string' => 'The model must be a string.',
            'model.in' => 'The selected model is invalid.',
            'text.required' => 'The text is required.',
            'text.string' => 'The text must be a string.',
            'text.min' => 'The text must be at least :min characters.',
            'text.max' => 'The text may not be greater than :max characters.',
            'voice.required' => 'The voice is required.',
            'voice.string' => 'The voice must be a string.',
            'voice.in' => 'The selected voice is invalid.',
        ];
    }
}
