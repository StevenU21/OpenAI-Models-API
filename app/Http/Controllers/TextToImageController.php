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

    public function getTextToImagePromptType(): JsonResponse
    {
        return $this->OpenAIService->getTextToImagePromptType();
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

        $responses = $this->OpenAIService->textToImage(
            $model,
            $prompt,
            $image_number,
            $quality,
            $size,
            $response_format,
            $style
        );

        $imageUrls = [];
        foreach ($responses as $response) {
            if ($response_format === 'url') {
                // Save image file from URL
                $imageUrl = $response['url'];
                $imageContent = Http::get($imageUrl)->body();
            } elseif ($response_format === 'b64_json') {
                // Save image file from base64
                $imageContent = base64_decode($response['b64_json']);
            }

            $imagePath = 'text_image_images/' . uniqid() . '.' . 'png';
            Storage::disk('public')->put($imagePath, $imageContent);
            $imageUrls[] = Storage::disk('public')->url($imagePath);
        }

        // Save the input text
        $textPath = 'text_image_texts/' . uniqid() . '.' . 'txt';
        Storage::disk('public')->put($textPath, $prompt);

        return response()->json(
            [
                'prompt' => $prompt,
                'image_urls' => $imageUrls,
                'text_url' => Storage::disk('public')->url($textPath),
            ]
        );
    }
}
