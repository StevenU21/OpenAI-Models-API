<?php

namespace App\Http\Controllers;

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
}
