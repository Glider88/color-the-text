<?php declare(strict_types=1);

use App\Http\Controllers\ColorTheTextController;
use App\Http\Middleware\TextFormatting;
use Illuminate\Support\Facades\Route;


Route::get('/upload', [ColorTheTextController::class, 'upload'])
    ->name('upload')
;

Route::get('/read/{id}', [ColorTheTextController::class, 'read'])
    ->name('read')
    ->whereNumber('id')
;

Route::post('/save', [ColorTheTextController::class, 'save'])
    ->name('save')
    ->middleware(TextFormatting::class)
;

Route::post('/finish', [ColorTheTextController::class, 'finish'])
    ->name('finish')
    ->middleware(TextFormatting::class)
;

Route::post('/delete', [ColorTheTextController::class, 'delete'])
    ->name('delete')
;

Route::get('/config', [ColorTheTextController::class, 'config'])
    ->name('config')
;
