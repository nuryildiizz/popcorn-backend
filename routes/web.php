<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\Api\HomeController;

Route::get('/api/home', [HomeController::class, 'index']);

use App\Http\Controllers\Api\TitleController;

Route::get('/api/titles', [TitleController::class, 'index']);
Route::get('/api/titles/{slug}', [TitleController::class, 'show']);
use App\Http\Controllers\Api\RandomController;

Route::get('/api/random', RandomController::class);
Route::get('/random', RandomController::class);
use App\Http\Controllers\Api\SearchController;

Route::get('/api/search', SearchController::class);


Route::get('/api/titles', [TitleController::class, 'index']);
Route::get('/api/titles/{slug}', [TitleController::class, 'show']);

use App\Http\Controllers\Api\MoodController;

Route::get('/api/moods', [MoodController::class, 'index']);
Route::get('/api/mood/{slug}', [MoodController::class, 'show']);

use App\Http\Controllers\Api\AuthController;

Route::post('/api/auth/register', [AuthController::class, 'register']);
Route::post('/api/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/me', [AuthController::class, 'me']);
    Route::post('/api/auth/logout', [AuthController::class, 'logout']);
});



