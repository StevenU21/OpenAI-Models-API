<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
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

        return [
            'bot_message' => $response['choices'][0]['message']['content'],
            'input_tokens' => $response['usage']['prompt_tokens'],
            'output_tokens' => $response['usage']['completion_tokens'],
        ];
    }
}
