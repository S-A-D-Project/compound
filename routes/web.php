<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompoundInterestController;
use App\Http\Controllers\TestConnectionController;

Route::get('/', [CompoundInterestController::class, 'index']);
Route::post('/calculate', [CompoundInterestController::class, 'store']);
Route::get('/test-connection', [TestConnectionController::class, 'test']);
Route::resource('compound-interest', CompoundInterestController::class);
