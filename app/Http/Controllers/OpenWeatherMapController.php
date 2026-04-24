<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherMapService;
use Carbon\Carbon;

class OpenWeatherMapController extends Controller
{
    protected $weatherService;

    public function __construct(OpenWeatherMapService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function testApiKey()
    {
        if ($this->weatherService->testApiKey()) {
            return $this->success(null, "API key is valid");
        }

        return $this->error('Invalid API key');
    }

    public function currentWeather(Request $request, $city = 'London')
    {
        $weatherData = $this->weatherService->getCurrentWeather($city);

        if ($weatherData) {
            return $this->success($weatherData, "Weather data fetched successfully");
        }

        return $this->error('Unable to fetch weather data');
    }

    public function cachedWeather(Request $request, $city = 'London')
    {
        $weatherData = $this->weatherService->getCachedWeather($city);

        if ($weatherData) {
            return $this->success($weatherData, "Weather data fetched from cache");
        }

        return $this->error('No cached weather data available');
    }
}
