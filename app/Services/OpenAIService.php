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
        $csvData = $response->getBody();

        // Remove the first line and last line
        $csvData = substr($csvData, strpos($csvData, "\n") + 1);
        $csvData = substr($csvData, 0, strrpos($csvData, "\n"));

        $prompts = [];
        foreach (explode("\n", $csvData) as $line) {
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

    public function streamedConversation($text, $model, $temperature, $prompt = 'You are a friendly chatbot.')
    {
        return response()->stream(function () use ($text, $model, $temperature, $prompt) {
            $messages = [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $text]
            ];

            $stream = OpenAI::chat()->createStreamed([
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => 1024,
            ]);

            foreach ($stream as $response) {
                $text = $response->choices[0]->delta->content;
                if (connection_aborted()) {
                    break;
                }

                echo "event: update\n";
                echo 'data: ' . $text;
                echo "\n\n";
                ob_flush();
                flush();
            }

            echo "event: update\n";
            echo 'data: <END_STREAMING_SSE>';
            echo "\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }
}
