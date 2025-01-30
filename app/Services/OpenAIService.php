<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    public function getPromptList(): JsonResponse
    {
        $response = Http::get('https://raw.githubusercontent.com/f/awesome-chatgpt-prompts/main/prompts.csv');

        if ($response->successful()) {
            $csvData = $response->body();
            $lines = explode(PHP_EOL, $csvData);
            $prompts = array_map('str_getcsv', $lines);
            return response()->json($prompts);
        }

        return response()->json([]);
    }

    public function getAIModels(): JsonResponse
    {
        $response = OpenAI::models()->list();

        return response()->json($response);
    }

    public function conversation($text, $model, $temperature, $prompt = 'You are a friendly chatbot.'): JsonResponse
    {
        $messages = [
            ['role' => 'system', 'content' => $prompt],
            ['role' => 'user', 'content' => $text]
        ];

        $response = OpenAI::chat()->create([
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
        ]);

        return response()->json([
            'bot_message' => $response['choices'][0]['message']['content'],
            'input_tokens' => $response['usage']['prompt_tokens'],
            'output_tokens' => $response['usage']['completion_tokens'],
        ]);
    }
}
