<?php

namespace App\Http\Controllers;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;

class SpeechToTextController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function getSpeechLanguages(): JsonResponse
    {
        return $this->OpenAIService->getSpeechLanguages();
    }

    public function getSpeechTextResponseFormats(): JsonResponse
    {
        return $this->OpenAIService->getSpeechTextResponseFormats();
    }
}
