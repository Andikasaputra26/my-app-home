<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gemini', [GeminiController::class, 'index'])->name('ai.gemini');
Route::post('/gemini/generate', [GeminiController::class, 'generate'])->name('ai.gemini.generate');