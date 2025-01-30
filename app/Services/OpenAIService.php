<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    public function getPromptList(): array
    {
        $response = Http::get('https://raw.githubusercontent.com/f/awesome-chatgpt-prompts/main/prompts.csv');

        if ($response->successful()) {
            $csvData = $response->body();
            $lines = explode(PHP_EOL, $csvData);
            $prompts = array_map('str_getcsv', $lines);
            return $prompts;
        }

        return [];
    }

    public function conversation($text, $model, $temperature, $prompt = 'You are a friendly chatbot.')
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
