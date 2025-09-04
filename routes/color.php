<?php declare(strict_types=1);

use App\Http\Controllers\ColorTheTextController;
use App\Http\Middleware\TextFormatting;
use Illuminate\Support\Facades\Route;


Route::get('/upload', [ColorTheTextController::class, 'upload'])->name('upload');
Route::get('/read', [ColorTheTextController::class, 'read'])->name('read');

Route
    ::post('/read', [ColorTheTextController::class, 'read'])
    ->name('save')
    ->middleware(TextFormatting::class)
;
