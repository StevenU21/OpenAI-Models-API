<?php

namespace App\Http\Controllers;
use App\Http\Requests\SpeechToTextRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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

    public function getSpeechTimestampGranularities(): JsonResponse
    {
        return $this->OpenAIService->getSpeechTimestampGranularities();
    }

    public function SpeechToText(SpeechToTextRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $language = $request->validated()['language'];
        $response_format = $request->validated()['response_format'];
        $temperature = $request->validated()['temperature'];
        $timestamp_granularities = $request->validated()['timestamp_granularities'];

        $response = $this->OpenAIService->SpeechToText($file, $language, $response_format, $temperature, $timestamp_granularities);

        // Guardar la transcripción en un archivo
        $filePath = 'speech_transcriptions/' . uniqid() . '.txt';
        Storage::disk('public')->put($filePath, (string) $response);

        // Generate the URL for the transcription file
        $audioUrl = Storage::disk('public')->url($filePath);

        return response()->json([
            'transcription_response' => $response,
            'transcription_url' => $audioUrl,
        ]);
    }
}
