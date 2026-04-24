<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Http\Resources\OpenWeatherMapResource;
use App\Enum\OpenWeatherMapEnum;
use Illuminate\Support\Facades\Cache;

class OpenWeatherMapService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('openweathermap.api_key');
        $this->baseUrl = config('openweathermap.base_url');
    }

    public function testApiKey()
    {
        $response = Http::get($this->baseUrl . 'weather', [
            'q' => 'London',
            'appid' => $this->apiKey,
        ]);

        return $response->successful();
    }

    // Handles all API requests to OpenWeatherMap, including error handling and logging
    public function apiRequest($endpoint, $params = [])
    {
        $response = Http::get($this->baseUrl . $endpoint, array_merge($params, [
            'appid' => $this->apiKey,
            'units' => 'metric',
        ]));

        if ($response->successful()) {
            return $response->json();
        }

        if ($response->status() === 401) {
            // Log invalid API key error
            \Log::error('OpenWeatherMap API key is invalid.');
        } else {
            // Log other API errors
            // This will help us identify issues like rate limits, server errors, or invalid requests
            // We don't want to show detailed error messages to the user, but we want to log them for debugging purposes
            \Log::error("OpenWeatherMap API request failed: {$response->status()} - {$response->body()}");
        }

        return null;
    }

    public function getCurrentWeather($city)
    {
        $response = $this->apiRequest('weather', [
            'q' => $city,
            'units' => 'metric',
        ]);

        if ($response) {
            Cache::put("openweathermap_q_{$city}", $response, now()->addMinutes(10));
            return new OpenWeatherMapResource($response, OpenWeatherMapEnum::EXTERNAL);
        }

        return null;
    }

    public function getCachedWeather($city)
    {
        $cachedData = Cache::get("openweathermap_q_{$city}");
        return $cachedData ? new OpenWeatherMapResource($cachedData, OpenWeatherMapEnum::CACHE) : null;
    }
}