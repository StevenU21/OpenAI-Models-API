<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;

class TextToSpeechController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function getTextToSpeechModels()
    {
        return $this->OpenAIService->getTextToSpeechModels();
    }

    public function getSpeechVoices()
    {
        return $this->OpenAIService->getSpeechVoices();
    }

    public function getSpeechVoicesAudio()
    {
        return $this->OpenAIService->getSpeechVoiceAudios();
    }
}
