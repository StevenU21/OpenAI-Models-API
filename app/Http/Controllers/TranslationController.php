<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;

class TranslationController extends Controller
{
    public function getLanguages(OpenAIService $OpenAIService): JsonResponse
    {
        return $OpenAIService->getLanguages();
    }

    public function translate(TranslationRequest $request, OpenAIService $OpenAIService): JsonResponse
    {
        $text = $request->validated()['text'];
        $sourceLanguage = $request->validated()['source_language'];
        $targetLanguage = $request->validated()['target_language'];

        $response = $OpenAIService->translate($text, $sourceLanguage, $targetLanguage);

        return response()->json([
            'translated_response' => $response,
        ]);
    }
}
