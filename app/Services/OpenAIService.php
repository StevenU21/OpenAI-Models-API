<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;

class OpenAIService
{
    public function getPromptList(): JsonResponse
    {
        $client = new Client();
        $response = $client->get('https://raw.githubusercontent.com/f/awesome-chatgpt-prompts/main/prompts.csv');
        $csvString = $response->getBody();

        // Remove the first line and last line
        $csvString = substr($csvString, strpos($csvString, "\n") + 1);
        $csvString = substr($csvString, 0, strrpos($csvString, "\n"));

        $prompts = [];
        foreach (explode("\n", $csvString) as $line) {
            $values = str_getcsv($line);
            $promptName = trim($values[0], '"');
            $promptDescription = trim($values[1], '"');
            $prompts[] = [
                'act' => $promptName,
                'prompt' => $promptDescription
            ];
        }

        return response()->json($prompts);
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
