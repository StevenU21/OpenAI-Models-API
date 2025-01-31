<?php

namespace Tests\Feature;

use App\Services\OpenAIService;
use Tests\TestCase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class TextToSpeechTest extends TestCase
{
    public function test_get_speech_models()
    {
        $response = $this->get('/api/text-to-speech/models');
        $response->assertStatus(200);
    }

    public function test_get_speech_voices()
    {
        $response = $this->get('/api/text-to-speech/voices');
        $response->assertStatus(200);
    }

    public function test_get_speech_voices_audio()
    {
        $response = $this->get('/api/text-to-speech/voices/audio');
        $response->assertStatus(200);
    }

    public function test_get_speech_languages()
    {
        $response = $this->get('/api/text-to-speech/languages');
        $response->assertStatus(200);
    }

    public function test_get_speech_response_formats()
    {
        $response = $this->get('/api/text-to-speech/response-formats');
        $response->assertStatus(200);
    }

    public function test_validation_text_is_required()
    {
        $response = $this->post('/api/text-to-speech', [
            'model' => 'tts-1',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_text_min()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'H',
            'model' => 'tts-1',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_text_max()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => str_repeat('a', 3001),
            'model' => 'tts-1',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_model_is_required()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'Hello, how are you?',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_model_is_in()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'Hello, how are you?',
            'model' => 'tts-2',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_voice_is_required()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'Hello, how are you?',
            'model' => 'tts-1',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_voice_is_in()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'Hello, how are you?',
            'model' => 'tts-1',
            'voice' => 'alloyy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_response_format_is_in()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'Hello, how are you?',
            'model' => 'tts-1',
            'voice' => 'alloy',
            'response_format' => 'mp33',
            'language' => 'en',
        ]);

        $response->assertStatus(302);
    }

    public function test_validation_language_is_in()
    {
        $response = $this->post('/api/text-to-speech', [
            'text' => 'Hello, how are you?',
            'model' => 'tts-1',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'eng',
        ]);

        $response->assertStatus(302);
    }

    public function test_text_to_speech()
    {
        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('textToSpeech')
                ->andReturn('fake_audio_content');
        });

        $response = $this->postJson('/api/text-to-speech', [
            'model' => 'tts-1',
            'text' => 'Hello, how are you?',
            'voice' => 'alloy',
            'response_format' => 'mp3',
            'language' => 'en',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'text',
            'audio_url',
        ]);
    }

    public function test_get_generated_speech_audios()
    {
        $response = $this->get('/api/text-to-speech/generated-audio');
        $response->assertStatus(200);
    }
}

