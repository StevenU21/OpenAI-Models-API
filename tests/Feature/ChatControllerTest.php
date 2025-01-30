<?php

namespace Tests\Feature;

use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    public function test_get_models()
    {
        $response = $this->get('/api/chat/models');
        $response->assertStatus(200);
    }

    public function test_get_prompts()
    {
        $response = $this->get('/api/chat/prompts');
        $response->assertStatus(200);
    }

    public function test_conversation()
    {
        $response = $this->post('/api/chat', [
            'text' => 'Hello, how are you?',
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.7,
            'prompt' => 'Hello, how are you?'
        ]);
        $response->assertStatus(200);
    }

    public function test_streamed_conversation_sse()
    {
        $response = $this->post('/api/chat/streamed', [
            'text' => 'Hello, how are you?',
            'model' => 'babbage-002',
            'temperature' => 0.7,
            'prompt' => 'Hello, how are you?'
        ]);
        $response->assertStatus(200);
    }
}
