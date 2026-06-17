<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DocumentController::class, 'home'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Content / discoverability pages (static, no DB).
Route::view('/security', 'pages.security')->name('security');
Route::view('/cli', 'pages.cli')->name('cli');
Route::view('/mcp', 'pages.mcp')->name('mcp');

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

// "Uploaded from this device" — a client-side list built from localStorage.
// No DB: the page is static; the registry lives only in the visitor's browser.
Route::view('/uploads', 'uploads')->name('uploads');

Route::get('/v/{id}/{slug?}', [DocumentController::class, 'viewer'])
    ->where('slug', '[A-Za-z0-9-]+')
    ->name('viewer');
Route::get('/e/{id}', [DocumentController::class, 'editor'])->name('editor');
