<?php

namespace Tests\Feature;

use Tests\TestCase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class TranslationControllerTest extends TestCase
{
    public function test_get_languages()
    {
        $response = $this->get('/api/translation/languages');
        $response->assertStatus(200);
    }

    public function test_translate()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Hola, ¿cómo estás?',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->post('/api/translation', [
            'text' => 'Hello, how are you?',
            'source_language' => 'en',
            'target_language' => 'es',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'translated_response' => 'Hola, ¿cómo estás?',
        ]);
    }

    public function test_translate_text_field_is_required()
    {
        $response = $this->post('/api/translation', [
            'source_language' => 'en',
            'target_language' => 'es',
        ]);
        $response->assertStatus(302);
    }

    public function test_translate_source_language_field_is_required()
    {
        $response = $this->post('/api/translation', [
            'text' => 'Hello, how are you?',
            'target_language' => 'es',
        ]);
        $response->assertStatus(302);
    }

    public function test_translate_target_language_field_is_required()
    {
        $response = $this->post('/api/translation', [
            'text' => 'Hello, how are you?',
            'source_language' => 'en',
        ]);
        $response->assertStatus(302);
    }

    public function test_translate_source_language_and_target_language_must_be_different()
    {
        $response = $this->post('/api/translation', [
            'text' => 'Hello, how are you?',
            'source_language' => 'en',
            'target_language' => 'en',
        ]);
        $response->assertStatus(302);
    }
}
