<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\RandomController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TitleController;
use App\Http\Controllers\Api\MoodController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\TmdbController;
use App\Http\Controllers\Api\TitleImportController;

Route::get('/tmdb/search', [TmdbController::class, 'search']);
Route::get('/tmdb/movie/{id}', [TmdbController::class, 'movie']);

Route::get('/home', [HomeController::class, 'index']);
Route::get('/random', RandomController::class);
Route::get('/search', [SearchController::class, 'search']);

Route::get('/titles', [TitleController::class, 'index']);
Route::get('/titles/{slug}', [TitleController::class, 'show']);

Route::get('/moods', [MoodController::class, 'index']);
Route::get('/mood/{slug}', [MoodController::class, 'show']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/me/favorites', [MeController::class, 'favorites']);
    Route::post('/me/favorites/{titleId}', [MeController::class, 'addFavorite']);
    Route::delete('/me/favorites/{titleId}', [MeController::class, 'removeFavorite']);

    Route::get('/me/watchlist', [MeController::class, 'watchlist']);
    Route::post('/me/watchlist/{titleId}', [MeController::class, 'addWatchlist']);
    Route::delete('/me/watchlist/{titleId}', [MeController::class, 'removeWatchlist']);

    Route::get('/me/watched', [MeController::class, 'watched']);
    Route::post('/me/watched/{titleId}', [MeController::class, 'addWatched']);
    Route::delete('/me/watched/{titleId}', [MeController::class, 'removeWatched']);

    Route::get('/me/settings', [MeController::class, 'settings']);
    Route::put('/me/settings', [MeController::class, 'updateSettings']);

    Route::put('/me/profile', [MeController::class, 'updateProfile']);
    Route::put('/me/password', [MeController::class, 'changePassword']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/admin/titles/import', [TitleImportController::class, 'import']);
});
