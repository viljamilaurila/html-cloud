<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Stateless document API (prefixed with /api by bootstrap/app.php).
// No sessions or CSRF: writes are authorized by edit_auth, a secret
// only the uploader holds. Used by the web app, CLI, and MCP clients.
Route::post('/documents', [DocumentController::class, 'store'])->middleware('throttle:20,60');
Route::get('/documents/{id}', [DocumentController::class, 'show']);
Route::put('/documents/{id}', [DocumentController::class, 'update']);
Route::patch('/documents/{id}/expiry', [DocumentController::class, 'updateExpiry']);
Route::patch('/documents/{id}/settings', [DocumentController::class, 'updateSettings']);
Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
