<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextToSpeechRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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

    public function getTextSpeechResponseFormats(): JsonResponse
    {
        return $this->OpenAIService->getTextSpeechResponseFormats();
    }

    public function textToSpeech(TextToSpeechRequest $request): JsonResponse
    {
        $model = $request->validated()['model'];
        $input = $request->validated()['input'];
        $voice = $request->validated()['voice'];
        $response_format = $request->validated()['response_format'];
        $speed = $request->validated()['speed'];
        $language = $request->validated()['language'];

        $response = $this->OpenAIService->textToSpeech($input, $voice, $model, $response_format, $speed, $language);

        // Save audio file
        $audioPath = 'audio_speech_audios/' . uniqid() . '.' . $response_format;
        Storage::disk('public')->put($audioPath, $response);

        // Save the input text
        $textPath = 'text_speech_audios/' . uniqid() . '.' . 'txt';
        Storage::disk('public')->put($textPath, $input);

        // Generate the URL for the audio file
        $audioUrl = Storage::disk('public')->url($audioPath);
        $textUrl = Storage::disk('public')->url($audioPath);

        return response()->json([
            'input' => $input,
            'audio_url' => $audioUrl,
            'text_url' => $textUrl,
        ]);
    }

    public function getGeneratedSpeechAudios(): JsonResponse
    {
        $audioFiles = Storage::disk('public')->files('audio_speech_audios');
        $textFiles = Storage::disk('public')->files('text_speech_audios');

        $audioData = array_map(function ($file) {
            return [
                'name' => basename($file),
                'audio_url' => Storage::disk('public')->url($file),
                'text_url' => Storage::disk('public')->url('text_speech_audios/' . basename($file)),
            ];
        }, $audioFiles);

        return response()->json($audioData);
    }

    public function textToSpeechStreamed(TextToSpeechRequest $request)
    {
        $validated = $request->validated();
        return $this->OpenAIService->textToSpeechStreamed($validated['input'], $validated['voice'], $validated['model'], $validated['response_format'], $validated['language'], $validated['language']);
    }
}
