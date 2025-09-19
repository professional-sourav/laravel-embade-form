<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function() {
    
    Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
});