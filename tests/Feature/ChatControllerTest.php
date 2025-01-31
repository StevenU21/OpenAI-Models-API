<?php

namespace Tests\Feature;

use Tests\TestCase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Models\ListResponse;

class ChatControllerTest extends TestCase
{
    public function test_get_models()
    {
        OpenAI::fake([
            ListResponse::fake([
                'data' => [
                    [
                        'id' => 'model-id-1',
                        'object' => 'model',
                        'created' => 1610070400,
                        'owned_by' => 'organization-owner',
                        'permission' => [],
                    ],
                    [
                        'id' => 'model-id-2',
                        'object' => 'model',
                        'created' => 1610070400,
                        'owned_by' => 'organization-owner',
                        'permission' => [],
                    ],
                ],
            ]),
        ]);

        $response = $this->get('/api/chat/models');
        $response->assertStatus(200);
    }

    public function test_conversation()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => '¡Hola! ¿Cómo puedo ayudarte hoy?',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->post('/api/chat', [
            'text' => 'Hello, how are you?',
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.7,
            'prompt' => 'Hello, how are you?',
        ]);

        $response->assertStatus(200);
    }

    public function test_streamed_conversation_sse()
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => '¡Hola! ¿Cómo puedo ayudarte hoy?',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->post('/api/chat/streamed', [
            'text' => 'Hello, how are you?',
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.7,
            'prompt' => 'Hello, how are you?',
        ]);

        $response->assertStatus(200);
    }
}
