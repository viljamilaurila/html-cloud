<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DocumentController::class, 'home'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Content / discoverability pages (static, no DB).
Route::view('/security', 'pages.security')->name('security');

// Comparison pages.
Route::view('/vs/netlify-drop', 'pages.vs.netlify-drop')->name('vs.netlify');
Route::view('/vs/codepen', 'pages.vs.codepen')->name('vs.codepen');
Route::view('/vs/google-drive-dropbox', 'pages.vs.google-drive-dropbox')->name('vs.drive');
Route::view('/vs/email-attachment', 'pages.vs.email-attachment')->name('vs.email');

// Use-case landing pages.
Route::view('/share-claude-artifact', 'pages.use.claude-artifact')->name('use.claude');
Route::view('/share-ai-presentation', 'pages.use.ai-presentation')->name('use.presentation');
Route::view('/send-private-client-report', 'pages.use.client-report')->name('use.report');
Route::view('/share-internal-document', 'pages.use.internal-document')->name('use.internal');

Route::get('/v/{id}', [DocumentController::class, 'viewer'])->name('viewer');
Route::get('/e/{id}', [DocumentController::class, 'editor'])->name('editor');

Route::prefix('api')->group(function () {
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{id}', [DocumentController::class, 'show']);
    Route::put('/documents/{id}', [DocumentController::class, 'update']);
    Route::patch('/documents/{id}/expiry', [DocumentController::class, 'updateExpiry']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
});
