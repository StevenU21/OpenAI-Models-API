<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use OpenAI\Laravel\Facades\OpenAI;
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

    public function getTextSpeechResponseFormats(): JsonResponse
    {
        $text_speech_response_format = [
            'mp3',
            'opus',
            'aac',
            'flac',
            'wav',
            'pcm',
        ];

        return response()->json($text_speech_response_format);
    }

    public function getSpeechTextResponseFormats()
    {
        $speech_text_response_formats = [
            'json',
            'text',
            'srt',
            'verbose_json',
            'vtt'
        ];

        return response()->json($speech_text_response_formats);
    }

    public function getSpeechTimestampGranularities(): JsonResponse
    {
        $speech_timestamp_granularities = [
            'word',
            'sentence',
            'segment',
        ];

        return response()->json($speech_timestamp_granularities);
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

    public function file_image_conversation($imageUrl): JsonResponse
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => "What's in this image?"],
                        ['type' => 'image_url', 'image_url' => ['url' => $imageUrl]],
                    ],
                ],
            ],
            'store' => true,
        ]);

        return response()->json([
            'bot_message' => $response['choices'][0]['message']['content'],
            'input_tokens' => $response['usage']['prompt_tokens'],
            'output_tokens' => $response['usage']['completion_tokens'],
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

    public function textToSpeech($input, $voice, $model = 'tts-1', $responseFormat = 'mp3', $speed = 1.0, $language = 'en')
    {
        $response = OpenAI::audio()->speech([
            'model' => $model,
            'input' => $input,
            'voice' => $voice,
            'response_format' => $responseFormat,
            'speed' => $speed,
            'language' => $language,
        ]);

        return $response;
    }

    public function textToSpeechStreamed($input, $voice, $model = 'tts-1', $responseFormat = 'mp3', $speed = 1.0, $language = 'en')
    {
        return response()->stream(function () use ($input, $voice, $model, $responseFormat, $speed, $language) {
            $stream = OpenAI::audio()->speechStreamed([
                'model' => $model,
                'input' => $input,
                'voice' => $voice,
                'response_format' => $responseFormat,
                'speed' => $speed,
                'language' => $language,
            ]);

            foreach ($stream as $chunk) {
                echo $chunk;
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function speechToText($filePath, $language, $response_format = 'verbose_json', $temperature = 0, $timestamp_granularities = 'segment')
    {
        $response = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($filePath, 'r'),
            'language' => $language,
            'response_format' => $response_format,
            'temperature' => $temperature,
            'timestamp_granularities[]' => $timestamp_granularities,
        ]);

        return $response;
    }

    public function getTextToImageModels(): JsonResponse
    {
        $images = [
            'dall-e-2',
            'dall-e-3'
        ];

        return response()->json($images);
    }

    public function getTextToImageQuality(): JsonResponse
    {
        $image_quality = [
            'standard',
            'hd'
        ];

        return response()->json($image_quality);
    }

    public function getTextToImageSizes(): JsonResponse
    {
        $image_sizes = [
            '256x256',
            '512x512',
            '1024x1024',
            '1024x1792',
            '1792x1024'
        ];

        return response()->json($image_sizes);
    }

    public function getTextToImageResponseFormats(): JsonResponse
    {
        $image_response_formats = [
            'url',
            'b64_json',
        ];

        return response()->json($image_response_formats);
    }

    public function getTextToImageStyle(): JsonResponse
    {
        $image_style = [
            'vivid',
            'natural'
        ];

        return response()->json($image_style);
    }

    public function getTextToImagePromptType(): JsonResponse
    {
        $promptDescriptions = [
            'realistic' => 'with photorealistic details, natural lighting, and true-to-life textures, capturing the essence of real-world visuals',
            'anime' => 'with vibrant colors, anime-style shading, exaggerated expressions, and dynamic poses',
            'cartoon' => 'with bold lines, simplistic yet expressive shapes, and bright, playful colors',
            'futuristic' => 'featuring advanced technology, sci-fi elements, neon lights, and a sense of otherworldly innovation',
            'abstract' => 'exploring surreal forms, blending colors and shapes in unexpected ways with an artistic, non-representational style',
            'impressionist' => 'with loose brushwork, an emphasis on light and color, evoking the fleeting nature of a moment through artistic interpretation',
            'pixel art' => 'with a retro, pixelated style reminiscent of early video games, featuring blocky graphics and limited color palettes',
            'watercolor' => 'with soft, flowing colors, gentle gradients, and a hand-painted aesthetic that mimics traditional watercolor techniques',
            'noir' => 'with high contrast, black-and-white tones, deep shadows, and a moody, cinematic atmosphere reminiscent of classic film noir',
            'steampunk' => 'with Victorian-era aesthetics, intricate mechanical details, steam-powered machinery, and an industrial yet antique vibe',
            'fantasy' => 'with mythical elements, enchanted landscapes, magical creatures, and a dream-like, otherworldly atmosphere',
            'vintage' => 'with retro aesthetics, film grain effects, muted colors, and a nostalgic feel that echoes bygone eras',
            'scifi' => 'with futuristic and cosmic elements, high-tech devices, neon glows, and an expansive, interstellar setting',
            'minimalist' => 'with clean lines, simplicity in design, abundant negative space, and a focus on essential elements for a modern look',
            'hyperrealistic' => 'with exquisite detail, lifelike textures, and realistic lighting that mimics real-world photography to the finest detail',
            'dramatic' => 'with intense lighting, high contrast, deep shadows, and a cinematic mood that emphasizes emotion and tension',
        ];

        return response()->json($promptDescriptions);
    }

    public function textToImage($model, $prompt, $type, $image_number = 1, $quality, $size, $response_format, $style): array
    {
        // Get the description for the prompt type
        $promptDescriptions = $this->getTextToImagePromptType()->getData(true);
        $typeDescription = $promptDescriptions[$type] ?? '';

        // Combine the prompt with the type description
        $combinedPrompt = $prompt . ' ' . $typeDescription;

        $response = OpenAI::images()->create([
            'model' => $model,
            'prompt' => $combinedPrompt,
            'n' => $image_number,
            'quality' => $quality,
            'size' => $size,
            'response_format' => $response_format,
            'style' => $style,
        ]);

        $images = [];
        foreach ($response['data'] as $data) {
            $images[] = [
                'url' => $data['url'],
                'b64_json' => $data['b64_json'] ?? null,
            ];
        }

        return $images;
    }
}
