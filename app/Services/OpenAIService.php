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

    public function getLanguages(): JsonResponse
    {
        $languages = [
            'Afrikaans' => 'af',
            'Albanian' => 'sq',
            'Amharic' => 'am',
            'Arabic' => 'ar',
            'Armenian' => 'hy',
            'Azerbaijani' => 'az',
            'Basque' => 'eu',
            'Belarusian' => 'be',
            'Bengali' => 'bn',
            'Bosnian' => 'bs',
            'Bulgarian' => 'bg',
            'Catalan' => 'ca',
            'Cebuano' => 'ceb',
            'Chichewa' => 'ny',
            'Chinese' => 'zh',
            'Corsican' => 'co',
            'Croatian' => 'hr',
            'Czech' => 'cs',
            'Danish' => 'da',
            'Dutch' => 'nl',
            'English' => 'en',
            'Esperanto' => 'eo',
            'Estonian' => 'et',
            'Filipino' => 'tl',
            'Finnish' => 'fi',
            'French' => 'fr',
            'Frisian' => 'fy',
            'Galician' => 'gl',
            'Georgian' => 'ka',
            'German' => 'de',
            'Greek' => 'el',
            'Gujarati' => 'gu',
            'Haitian Creole' => 'ht',
            'Hausa' => 'ha',
            'Hawaiian' => 'haw',
            'Hebrew' => 'he',
            'Hindi' => 'hi',
            'Hmong' => 'hmn',
            'Hungarian' => 'hu',
            'Icelandic' => 'is',
            'Igbo' => 'ig',
            'Indonesian' => 'id',
            'Irish' => 'ga',
            'Italian' => 'it',
            'Japanese' => 'ja',
            'Javanese' => 'jw',
            'Kannada' => 'kn',
            'Kazakh' => 'kk',
            'Khmer' => 'km',
            'Kinyarwanda' => 'rw',
            'Korean' => 'ko',
            'Kurdish (Kurmanji)' => 'ku',
            'Kyrgyz' => 'ky',
            'Lao' => 'lo',
            'Latin' => 'la',
            'Latvian' => 'lv',
            'Lithuanian' => 'lt',
            'Luxembourgish' => 'lb',
            'Macedonian' => 'mk',
            'Malagasy' => 'mg',
            'Malay' => 'ms',
            'Malayalam' => 'ml',
            'Maltese' => 'mt',
            'Maori' => 'mi',
            'Marathi' => 'mr',
            'Mongolian' => 'mn',
            'Myanmar (Burmese)' => 'my',
            'Nepali' => 'ne',
            'Norwegian' => 'no',
            'Odia (Oriya)' => 'or',
            'Pashto' => 'ps',
            'Persian' => 'fa',
            'Polish' => 'pl',
            'Portuguese' => 'pt',
            'Punjabi' => 'pa',
            'Romanian' => 'ro',
            'Russian' => 'ru',
            'Samoan' => 'sm',
            'Scots Gaelic' => 'gd',
            'Serbian' => 'sr',
            'Sesotho' => 'st',
            'Shona' => 'sn',
            'Sindhi' => 'sd',
            'Sinhala' => 'si',
            'Slovak' => 'sk',
            'Slovenian' => 'sl',
            'Somali' => 'so',
            'Spanish' => 'es',
            'Sundanese' => 'su',
            'Swahili' => 'sw',
            'Swedish' => 'sv',
            'Tajik' => 'tg',
            'Tamil' => 'ta',
            'Tatar' => 'tt',
            'Telugu' => 'te',
            'Thai' => 'th',
            'Turkish' => 'tr',
            'Turkmen' => 'tk',
            'Ukrainian' => 'uk',
            'Urdu' => 'ur',
            'Uyghur' => 'ug',
            'Uzbek' => 'uz',
            'Vietnamese' => 'vi',
            'Welsh' => 'cy',
            'Xhosa' => 'xh',
            'Yiddish' => 'yi',
            'Yoruba' => 'yo',
            'Zulu' => 'zu',
        ];

        return response()->json($languages);
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

    public function textToSpeech($text, $voice, $model = 'tts-1', $responseFormat = 'mp3')
    {
        $response = OpenAI::audio()->speech([
            'model' => $model,
            'input' => $text,
            'voice' => $voice,
            'response_format' => $responseFormat,
        ]);

        return response()->json($response);
    }
}
