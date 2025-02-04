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
        // Add authorization logic if needed
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
            'type' => ['required', 'string', 'in:realistic,anime,cartoon,futuristic,abstract,impressionist,pixel art,watercolor,noir,steampunk,fantasy,vintage,scifi,minimalist,hyperrealistic,dramatic'],
            'image_number' => ['integer', 'min:1', 'max:10'],
            'style' => ['string'],
            'size' => ['required', 'string'],
            'response_format' => ['string', 'in:url,b64_json'],
            'quality' => ['nullable', 'string'],
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

        $validator->sometimes('image_number', 'max:1', function ($input) {
            return $input->model === 'dall-e-3';
        });

        $validator->sometimes('image_number', 'max:10', function ($input) {
            return $input->model === 'dall-e-2';
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

    public function messages(): array
    {
        return [
            'model.in' => 'The model must be either dall-e-2 or dall-e-3.',
            'prompt.min' => 'The prompt must be at least 8 characters.',
            'prompt.max' => 'The prompt must be at most 1000 characters for dall-e-2 and 4000 characters for dall-e-3.',
            'type.in' => 'The type must be one of the following: realistic, anime, cartoon, futuristic, abstract, impressionist, pixel art, watercolor, noir, steampunk, fantasy, vintage, scifi, minimalist, hyperrealistic, dramatic.',
            'size.in' => 'The size must be either 256x256, 512x512, or 1024x1024 for dall-e-2 and 1024x1024, 1792x1024, or 1024x1792 for dall-e-3.',
            'image_number.max' => 'The image number must be at most 1 for dall-e-3 and 1 to 10 for dall-e-2.',
            'style.in' => 'The style must be either vivid or natural for dall-e-3.',
            'response_format.in' => 'The response format must be either url or b64_json.',
            'quality.in' => 'The quality must be either standard or hd for dall-e-3.',
        ];
    }
}
