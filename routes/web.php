<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkKgbDownloadController;
use App\Http\Controllers\HelpController;

Route::get('/', function () {
    return view('welcome');
});

// Secure download routes for SK documents with watermark
Route::middleware(['auth'])->group(function () {
    Route::get('/sk-kgb/{skKgb}/download', [SkKgbDownloadController::class, 'download'])
        ->name('sk-kgb.download');

    Route::get('/sk-kgb/{skKgb}/preview', [SkKgbDownloadController::class, 'preview'])
        ->name('sk-kgb.preview');
});

// Help and FAQ routes
Route::middleware(['auth'])->group(function () {
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/help/faq', [HelpController::class, 'faq'])->name('help.faq');
    Route::get('/help/guide', [HelpController::class, 'guide'])->name('help.guide');
    Route::get('/help/videos', [HelpController::class, 'videoTutorials'])->name('help.videos');
});

// Download routes
Route::middleware(['auth'])->group(function () {
    Route::get('/downloads/document-template/{documentTemplate}', [DownloadController::class, 'downloadDocumentTemplate'])
        ->name('downloads.document-template');
});
