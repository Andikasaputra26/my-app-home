<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\Auth\AuthController;

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

Route::middleware('auth')->group(function () {
    Route::get('/gemini', [GeminiController::class, 'index'])->name('ai.gemini');
    Route::post('/gemini/generate', [GeminiController::class, 'generate'])->name('ai.gemini.generate');
});

Route::get("/register", [AuthController::class, "register"])->name("register");
Route::post("/register", [AuthController::class, "store"])->name("register.store");
Route::get("/login", [AuthController::class, "login"])->name("login");
Route::post("/login", [AuthController::class, "authenticate"])->name("login.authenticate");

Route::get('/', function () {
    return view('welcome');
});

