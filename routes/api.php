<?php

use App\Http\Controllers\DocumentController;
use App\Http\Middleware\VerifyEditKey;
use Illuminate\Support\Facades\Route;

// Stateless document API (prefixed with /api by bootstrap/app.php). No sessions
// or CSRF: writes are authorized by the edit key, a secret only the uploader
// holds. Used by the web app, CLI, browser extension and MCP clients.
Route::post('/documents', [DocumentController::class, 'store'])->middleware('throttle:20,60');
Route::get('/documents/{document}', [DocumentController::class, 'show']);

Route::middleware(VerifyEditKey::class)->group(function () {
    Route::put('/documents/{document}', [DocumentController::class, 'update']);
    Route::patch('/documents/{document}/expiry', [DocumentController::class, 'updateExpiry']);
    Route::patch('/documents/{document}/settings', [DocumentController::class, 'updateSettings']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
});
