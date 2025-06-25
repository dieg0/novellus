<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;

Route::get('/user', fn (Request $request) => $request->user())
    ->middleware('auth:sanctum');

Route::post('/loan/calculate', [LoanController::class, 'calculate']);
