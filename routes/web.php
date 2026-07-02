<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\PercakapanController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LogApiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ChatbotController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('kontak', KontakController::class);

Route::get('/pesan', [WhatsAppController::class, 'index'])->name('pesan.index');
Route::post('/pesan/send', [WhatsAppController::class, 'send'])->name('pesan.send');

Route::get('/percakapan', [PercakapanController::class, 'index'])->name('percakapan.index');
Route::get('/percakapan/{percakapan}', [PercakapanController::class, 'show'])->name('percakapan.show');

Route::get('/log-api', [LogApiController::class, 'index'])->name('log.api');
Route::get('/log-webhook', [LogApiController::class, 'webhook'])->name('log.webhook');

Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');

Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/settings', [ChatbotController::class, 'updateSettings'])->name('chatbot.settings.update');
Route::post('/chatbot/menu', [ChatbotController::class, 'storeMenu'])->name('chatbot.menu.store');
Route::put('/chatbot/menu/{menu}', [ChatbotController::class, 'updateMenu'])->name('chatbot.menu.update');
Route::delete('/chatbot/menu/{menu}', [ChatbotController::class, 'deleteMenu'])->name('chatbot.menu.delete');
Route::post('/chatbot/keyword', [ChatbotController::class, 'storeKeyword'])->name('chatbot.keyword.store');
Route::put('/chatbot/keyword/{keyword}', [ChatbotController::class, 'updateKeyword'])->name('chatbot.keyword.update');
Route::delete('/chatbot/keyword/{keyword}', [ChatbotController::class, 'deleteKeyword'])->name('chatbot.keyword.delete');

Route::get('/webhook', [WebhookController::class, 'verify']);
Route::post('/webhook', [WebhookController::class, 'handle']);
