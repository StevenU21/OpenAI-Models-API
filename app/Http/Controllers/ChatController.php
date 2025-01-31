<?php

namespace App\Http\Controllers;
use App\Http\Requests\ChatRequest;
use App\Http\Requests\TranslationRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function getModels(): JsonResponse
    {
        return $this->OpenAIService->getAIModels();
    }

    public function getPrompts(): JsonResponse
    {
        return $this->OpenAIService->getPromptList();
    }

    public function conversation(ChatRequest $request): JsonResponse
    {
        $text = $request->validated()['text'];
        $model = $request->validated()['model'];
        $temperature = $request->validated()['temperature'];
        $prompt = $request->validated()['prompt'];

        return $this->OpenAIService->conversation($text, $model, $temperature, $prompt);
    }

    public function streamed_conversation_sse(ChatRequest $request)
    {
        $text = $request->validated()['text'];
        $model = $request->validated()['model'];
        $temperature = $request->validated()['temperature'];
        $prompt = $request->validated()['prompt'];

        return $this->OpenAIService->streamedConversationSSE($text, $model, $temperature, $prompt);
    }
}
