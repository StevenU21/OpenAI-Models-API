<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\TextToSpeechController;
use App\Http\Controllers\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/models', [ChatController::class, 'getModels'])->name('models');
    Route::get('/prompts', [ChatController::class, 'getPrompts'])->name('prompts');
    Route::post('/', [ChatController::class, 'conversation'])->name('conversation');
    Route::post('/streamed', [ChatController::class, 'streamed_conversation_sse'])->name('streamed.conversation');
});

Route::prefix('translation')->name('translation.')->group(function () {
    Route::get('/languages', [TranslationController::class, 'getLanguages'])->name('languages');
    Route::post('/', [TranslationController::class, 'translate'])->name('translate');
});

Route::prefix('text-to-speech')->name('text-to-speech.')->group(function () {
    Route::get('/models', [TextToSpeechController::class, 'getTextToSpeechModels'])->name('models');
    Route::get('/voices', [TextToSpeechController::class, 'getSpeechVoices'])->name('voices');
    Route::get('/voices/audio', [TextToSpeechController::class, 'getSpeechVoicesAudio'])->name('voices.audio');
});
