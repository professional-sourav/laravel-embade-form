<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\UserController::class, 'index'])
->middleware(['auth'])
->name('dashboard');

Route::resource('posts', \App\Http\Controllers\PostController::class);

// require __DIR__.'/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


use App\Http\Controllers\EmbedFormController;


// Make sure CSRF is disabled for these routes
Route::middleware(['web'])->prefix('embed')->group(function () {
    Route::get('/form', [EmbedFormController::class, 'show'])->name('embed.form.show');

    // POST route needs to exclude CSRF verification
    Route::post('/form/submit', [EmbedFormController::class, 'submit'])
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->name('embed.form.submit');
});
