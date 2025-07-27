<?php declare(strict_types=1);

use App\Http\Controllers\LabController;
use Illuminate\Support\Facades\Route;


Route::get('/lab', [LabController::class, 'index']);
