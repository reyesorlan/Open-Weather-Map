<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureAPITokenIsValid;

// Suggested API versioning with prefix
Route::prefix('v1')->middleware(EnsureAPITokenIsValid::class)->group(function () {

    Route::prefix('openweathermap')->group(function () {
        Route::get('/test-api-key', [App\Http\Controllers\OpenWeatherMapController::class, 'testApiKey']);

        Route::prefix('weather')->group(function () {
            Route::get('/{city}', [App\Http\Controllers\OpenWeatherMapController::class, 'currentWeather']);
            Route::get('/{city}/cached', [App\Http\Controllers\OpenWeatherMapController::class, 'cachedWeather']);
        });

    });

});

// Test base requests without version prefix
Route::prefix('weather')->middleware(EnsureAPITokenIsValid::class)->group(function () {
    Route::get('/{city}', [App\Http\Controllers\OpenWeatherMapController::class, 'currentWeather']);
    Route::get('/{city}/cached', [App\Http\Controllers\OpenWeatherMapController::class, 'cachedWeather']);
});

// Test API key validation without version prefix
Route::middleware(EnsureAPITokenIsValid::class)->get('/test-api-key', [App\Http\Controllers\OpenWeatherMapController::class, 'testApiKey']);
