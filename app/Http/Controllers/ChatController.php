<?php

namespace App\Http\Controllers;
use App\Services\OpenAIService;

class ChatController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }
}
