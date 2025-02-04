<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextToImageRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;

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
        $style = $request->validated()['style'];
        $size = $request->validated()['size'];
        $response_format = $request->validated()['response_format'];
        $quality = $request->validated()['quality'];

        return response()->json(200);
    }
}
