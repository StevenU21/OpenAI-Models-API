<?php

use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/models', [ChatController::class, 'getModels'])->name('models');
    Route::get('/prompts', [ChatController::class, 'getPrompts'])->name('prompts');
    Route::post('/', [ChatController::class, 'conversation'])->name('store');
    Route::post('/streamed', [ChatController::class, 'streamed_conversation_sse'])->name('streamed');
});
