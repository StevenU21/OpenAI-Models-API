<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TextToImageRequest extends FormRequest
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
            'model' => ['required', 'string', 'in:dall-e-2,dall-e-3'],
            'prompt' => ['required', 'string', 'min:8'],
            'style' => ['string'],
            'size' => ['required', 'string'],
            'quality' => ['string'],
        ];
    }

    protected function withValidator(Validator $validator)
    {
        $validator->sometimes('prompt', 'max:1000', function ($input) {
            return $input->model === 'dall-e-2';
        });

        $validator->sometimes('prompt', 'max:4000', function ($input) {
            return $input->model === 'dall-e-3';
        });

        $validator->sometimes('size', 'in:256x256,512x512,1024x1024', function ($input) {
            return $input->model === 'dall-e-2';
        });

        $validator->sometimes('size', 'in:1024x1024,1792x1024,1024x1792', function ($input) {
            return $input->model === 'dall-e-3';
        });

        $validator->sometimes('style', 'required|string|in:vivid,natural', function ($input) {
            return $input->model === 'dall-e-3';
        });

        $validator->sometimes('quality', 'in:standard', function ($input) {
            return $input->model === 'dall-e-2';
        });

        $validator->sometimes('quality', 'in:standard,hd', function ($input) {
            return $input->model === 'dall-e-3';
        });
    }
}
