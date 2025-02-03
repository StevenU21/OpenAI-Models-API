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

    public function getSpeechToTextActions(): JsonResponse
    {
        return $this->OpenAIService->getSpeechToTextActions();
    }

    public function SpeechToText(SpeechToTextRequest $request): JsonResponse
    {
        $data = $request->validated();
        $file = $request->file('file');
        $action = $data['action'];

        $audioFilePath = $file->store('speech_text_audios', 'public');
        $audioUrl = Storage::disk('public')->url($audioFilePath);

        $response = $this->OpenAIService->SpeechToText(
            $audioUrl,
            $data['language'],
            $data['response_format'],
            $data['temperature'],
            $data['timestamp_granularities'],
            $action
        );

        $actionText = $response->text;
        $actionFilePath = 'speech_text_' . $action . '/' . $action . '_' . uniqid() . '.txt';
        Storage::disk('public')->put($actionFilePath, $actionText);
        $actionUrl = Storage::disk('public')->url($actionFilePath);

        return response()->json([
            $action . '_response' => $actionText,
            $action . '_url' => $actionUrl,
            'audio_url' => $audioUrl,
        ]);
    }
}
