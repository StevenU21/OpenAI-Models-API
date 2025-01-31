<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use OpenAI\Laravel\Facades\OpenAI;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

class OpenAIService
{
    public function getPromptList(): JsonResponse
    {
        $filePath = public_path('prompts/prompts.csv');
        $prompts = [];

        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r');
            fgets($file);

            while (($line = fgets($file)) !== false) {
                $values = str_getcsv($line);
                $promptName = trim($values[0], '"');
                $promptDescription = trim($values[1], '"');
                $prompts[] = [
                    'act' => $promptName,
                    'prompt' => $promptDescription
                ];
            }
            fclose($file);
        }

        return response()->json($prompts);
    }

    public function getAIModels(): JsonResponse
    {
        $response = OpenAI::models()->list();

        return response()->json($response);
    }

    public function getTextToSpeechModels(): JsonResponse
    {
        $speech_audio_models = [
            'tts-1',
            'tts-1-hd'
        ];

        return response()->json($speech_audio_models);
    }

    public function getSpeechVoices(): JsonResponse
    {
        $voices = [
            'alloy',
            'ash',
            'coral',
            'echo',
            'fable',
            'onyx',
            'nova',
            'sage',
            'shimmer',
        ];

        return response()->json($voices);
    }

    public function getSpeechVoiceAudios(): JsonResponse
        {
            $audioPath = public_path('speech_voices');
            $audioFiles = File::files($audioPath);

            $audioList = [];
            foreach ($audioFiles as $file) {
                $audioList[] = asset('speech_voices/' . $file->getFilename());
            }

            return response()->json($audioList);
        }

    private function readLanguagesFromFile(string $filePath): array
    {
        $languages = [];

        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r');
            while (($line = fgets($file)) !== false) {
                $parts = explode(': ', trim($line));
                if (count($parts) == 2) {
                    $languages[$parts[0]] = $parts[1];
                }
            }
            fclose($file);
        }

        return $languages;
    }

    public function getLanguages(): JsonResponse
    {
        $filePath = public_path('languages/languages.txt');
        $languages = $this->readLanguagesFromFile($filePath);

        return response()->json($languages);
    }

    public function getSpeechLanguages(): JsonResponse
    {
        $filePath = public_path('languages/speech_languages.txt');
        $languages = $this->readLanguagesFromFile($filePath);

        return response()->json($languages);
    }

    public function getSpeechResponseFormats(): JsonResponse
    {
        $responseFormats = [
            'mp3',
            'opus',
            'aac',
            'flac',
            'wav',
            'pcm',
        ];

        return response()->json($responseFormats);
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

    public function streamedConversationSSE($text, $model = 'gpt-4o-mini', $temperature = 0.5, $prompt = 'You are a friendly chatbot.')
    {
        return response()->stream(function () use ($text, $model, $temperature, $prompt) {
            $stream = OpenAI::chat()->createStreamed([
                'model' => $model,
                'temperature' => $temperature,
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => $text]
                ],
            ]);

            foreach ($stream as $response) {
                $text = $response->choices[0]->delta->content;
                if (connection_aborted()) {
                    break;
                }

                echo "event: update\n";
                echo 'data: ' . json_encode(['content' => $text]);
                echo "\n\n";
                ob_flush();
                flush();
            }

            echo "event: end\n";
            echo "data: {}\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function translate($text, $sourceLanguage, $targetLanguage)
    {
        $messages = [
            ['role' => 'system', 'content' => "You are a translator."],
            ['role' => 'user', 'content' => "Translate the following text from $sourceLanguage to $targetLanguage: $text"]
        ];

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 70,
            'temperature' => 0,
        ]);

        return trim($response['choices'][0]['message']['content']);
    }

    public function textToSpeech($text, $voice, $model = 'tts-1', $responseFormat = 'mp3', $language = 'en')
    {
        $response = OpenAI::audio()->speech([
            'model' => $model,
            'input' => $text,
            'voice' => $voice,
            'response_format' => $responseFormat,
            'language' => $language,
        ]);

        return $response;
    }
}
