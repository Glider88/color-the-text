<?php declare(strict_types=1);

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;


Route::get('/lab/count', [LabController::class, 'count']);
Route::get('/lab', [LabController::class, 'lab']);
Route::get('/lab/ping', [LabController::class, 'ping']);
Route::get('/lab/llm', [LabController::class, 'llm']);
