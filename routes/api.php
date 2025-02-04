<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\SpeechToTextController;
use App\Http\Controllers\TextToImageController;
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
    Route::post('/image', [ChatController::class, 'image_conversation'])->name('image.conversation');
});

Route::prefix('translation')->name('translation.')->group(function () {
    Route::get('/languages', [TranslationController::class, 'getLanguages'])->name('languages');
    Route::post('/', [TranslationController::class, 'translate'])->name('translate');
});

Route::prefix('text-to-speech')->name('text-to-speech.')->group(function () {
    Route::get('/models', [TextToSpeechController::class, 'getTextToSpeechModels'])->name('models');
    Route::get('/voices', [TextToSpeechController::class, 'getSpeechVoices'])->name('voices');
    Route::get('/voices/audio', [TextToSpeechController::class, 'getSpeechVoicesAudio'])->name('voices.audio');
    Route::get('/languages', [TextToSpeechController::class, 'getSpeechLanguages'])->name('languages');
    Route::get('/response-formats', [TextToSpeechController::class, 'getTextSpeechResponseFormats'])->name('response-formats');
    Route::post('/', [TextToSpeechController::class, 'textToSpeech'])->name('text-to-speech');
    Route::get('/generated-audio', [TextToSpeechController::class, 'getGeneratedSpeechAudios'])->name('generate-audio');
    Route::post('/streamed', [TextToSpeechController::class, 'textToSpeechStreamed'])->name('streamed');
});

Route::prefix('speech-to-text')->name('speech-to-text.')->group(function () {
    Route::get('/languages', [SpeechToTextController::class, 'getSpeechLanguages'])->name('languages');
    Route::get('/response-formats', [SpeechToTextController::class, 'getSpeechTextResponseFormats'])->name('response-formats');
    Route::get('/timestamp-granularities', [SpeechToTextController::class, 'getSpeechTimestampGranularities'])->name('timestamp-granularities');
    Route::get('/actions', [SpeechToTextController::class, 'getSpeechToTextActions'])->name('actions');
    Route::post('/', [SpeechToTextController::class, 'speechToText'])->name('speech-to-text');
});

Route::prefix('text-to-image')->name('text-to-image.')->group(function () {
    Route::get('/models', [TextToImageController::class, 'getTextToImageModels'])->name('models');
    Route::get('/quality', [TextToImageController::class, 'getTextToImageQuality'])->name('quality');
    Route::get('/sizes', [TextToImageController::class, 'getTextToImageSizes'])->name('sizes');
    Route::get('/prompt', [TextToImageController::class, 'getTextToImagePrompt'])->name('prompt');
    Route::get('/response-formats', [TextToImageController::class, 'getTextToImageResponseFormats'])->name('response-formats');
    Route::get('/style', [TextToImageController::class, 'getTextToImageStyle'])->name('style');
    Route::post('/', [TextToImageController::class, 'textToImage'])->name('text-to-image');
});
