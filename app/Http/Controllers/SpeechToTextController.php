<?php

namespace App\Http\Controllers;
use App\Http\Requests\SpeechToTextRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $data = $request->validated();
        $file = $request->file('file');

        // Store the audio file and get the URL
        $audioFilePath = $file->store('request_speech_audios', 'public');
        $audioUrl = Storage::disk('public')->url($audioFilePath);

        // Call the OpenAIService SpeechToText method
        $response = $this->OpenAIService->SpeechToText(
            $audioUrl,
            $data['language'],
            $data['response_format'],
            $data['temperature'],
            $data['timestamp_granularities']
        );

        // Store the transcription text in a file and get the URL
        $transcriptionText = $response->text;
        $transcriptionFilePath = 'speech_transcriptions/transcription_' . uniqid() . '.txt';
        Storage::disk('public')->put($transcriptionFilePath, $transcriptionText);
        $transcriptionUrl = Storage::disk('public')->url($transcriptionFilePath);

        return response()->json([
            'transcription_response' => $transcriptionText,
            'transcription_url' => $transcriptionUrl,
            'audio_url' => $audioUrl,
        ]);
    }
}
