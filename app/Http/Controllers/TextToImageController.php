<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextToImageRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class TextToImageController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function getTextToImageModels(): JsonResponse
    {
        return $this->OpenAIService->getTextToImageModels();
    }

    public function getTextToImageQuality(): JsonResponse
    {
        return $this->OpenAIService->getTextToImageQuality();
    }

    public function getTextToImageSizes(): JsonResponse
    {
        return $this->OpenAIService->getTextToImageSizes();
    }

    public function getTextToImagePrompt(): JsonResponse
    {
        return $this->OpenAIService->getTextToImagePrompt();
    }

    public function getTextToImageResponseFormats(): JsonResponse
    {
        return $this->OpenAIService->getTextToImageResponseFormats();
    }

    public function getTextToImageStyle(): JsonResponse
    {
        return $this->OpenAIService->getTextToImageStyle();
    }

    public function TextToImage(TextToImageRequest $request): JsonResponse
    {
        $model = $request->validated()['model'];
        $prompt = $request->validated()['prompt'];
        $image_number = $request->validated()['image_number'];
        $quality = $request->validated()['quality'];
        $size = $request->validated()['size'];
        $response_format = $request->validated()['response_format'];
        $style = $request->validated()['style'];

        $response = $this->OpenAIService->textToImage(
            $model,
            $prompt,
            $image_number,
            $quality,
            $size,
            $response_format,
            $style
        );

        // Save image file
        $imageUrl = $response['url'];
        $imageContent = Http::get($imageUrl)->body();

        $imagePath = 'text_image_images/' . uniqid() . '.' . 'png';
        Storage::disk('public')->put($imagePath, $imageContent);

        // Save the input text
        $textPath = 'text_image_texts/' . uniqid() . '.' . 'txt';
        Storage::disk('public')->put($textPath, $prompt);

        return response()->json(
            [
                'prompt' => $prompt,
                'image_url' => Storage::disk('public')->url($imagePath),
                'text_url' => Storage::disk('public')->url($textPath),
            ]
        );
    }
}
