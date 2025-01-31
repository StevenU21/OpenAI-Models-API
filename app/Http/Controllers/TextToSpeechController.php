<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextToSpeechRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;

class TextToSpeechController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function getTextToSpeechModels(): JsonResponse
    {
        return $this->OpenAIService->getTextToSpeechModels();
    }

    public function getSpeechVoices(): JsonResponse
    {
        return $this->OpenAIService->getSpeechVoices();
    }

    public function getSpeechVoicesAudio(): JsonResponse
    {
        return $this->OpenAIService->getSpeechVoiceAudios();
    }

    public function getSpeechLanguages(): JsonResponse
    {
        return $this->OpenAIService->getSpeechLanguages();
    }

    public function getSpeechResponseFormats(): JsonResponse
    {
        return $this->OpenAIService->getSpeechResponseFormats();
    }

    public function textToSpeech(TextToSpeechRequest $request): JsonResponse
    {
        $model = $request->validated()['model'];
        $text = $request->validated()['text'];
        $voice = $request->validated()['voice'];
        $response_format = $request->validated()['response_format'];
        $language = $request->validated()['language'];

        $response = $this->OpenAIService->textToSpeech($text, $voice, $model, $response_format, $language);

        return response()->json([
            'audio' => $response,
        ]);
    }
}
