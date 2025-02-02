<?php

namespace App\Http\Controllers;
use App\Http\Requests\ChatRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

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

    public function image_conversation(Request $request)
    {
        // $request->validate([
        //     'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        $image = $request->input('image');

        // $imagefilePath = $image->store('conversation_images', 'public');
        // $imageUrl = Storage::disk('public')->url($imagefilePath);

        return $this->OpenAIService->file_image_conversation($image);
    }
}
