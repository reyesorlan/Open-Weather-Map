<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class OpenWeatherMapTest extends TestCase
{

    private const CITY = 'London';
    private const CACHE_KEY = 'openweathermap_q_' . self::CITY;
    private const API_PREFIX = '/api/v1/openweathermap';
    private const APP_API_KEY = 'CrnMJHhaHkdT3QpNsNVf9qwjAKHuKg72';

    public function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test to ensure consistent results
        \Illuminate\Support\Facades\Cache::flush();
    }

    // Basic test to ensure the application is running
    public function test_api_status(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // Test API key validation with missing API key
    public function test_api_with_wrong_api_key(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => 'invalid_key'])
                         ->get(self::API_PREFIX . '/test-api-key');

        $response->assertStatus(401)
                 ->assertJson(['status' => 'error', 'message' => 'Invalid API key']);
    }

    // Test API key validation endpoint
    public function test_v1_openweathermap_test_api_key(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                         ->get(self::API_PREFIX . '/test-api-key');

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success', 'message' => 'API key is valid', 'data' => null]);
    }

    // Test fetching current weather for a city from cache when no cache exists
    public function test_v1_openweathermap_weather_city_if_cached_is_null(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                         ->get(self::API_PREFIX . '/weather/' . self::CITY . '/cached');

        $response->assertStatus(400)
                 ->assertJsonStructure(['status', 'message', 'data']);
    }

    // Test fetching current weather for a city from external API
    public function test_v1_openweathermap_weather_city_from_external_api(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                         ->get(self::API_PREFIX . '/weather/' . self::CITY);

        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'message', 'data' => ['city', 'temperature', 'description', 'timestamp', 'source']]);
    }

    // Test that weather data is stored in cache after fetching from external API
    public function test_v1_openweathermap_weather_city_stored_cache_successfully(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                         ->get(self::API_PREFIX . '/weather/' . self::CITY);

        $this->assertTrue(Cache::has(self::CACHE_KEY));
    }

    // Test fetching current weather for a city from external API and returning correct source in response
    public function test_v1_openweathermap_weather_city_from_external_api_source(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                         ->get(self::API_PREFIX . '/weather/' . self::CITY);

        $response->assertStatus(200)
                 ->assertJson(['status' => 'success', 'message' => 'Weather data fetched successfully', 'data' => ['source' => 'external']]);
    }
    
    // Test fetching current weather for a city from cache and returning correct source in response
    public function test_v1_openweathermap_weather_city_from_cache_source(): void
    {

        $response = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                         ->get(self::API_PREFIX . '/weather/' . self::CITY);

        $cached = $this->withHeaders(['X-API-KEY' => self::APP_API_KEY])
                       ->get(self::API_PREFIX . '/weather/' . self::CITY . '/cached');

        $cached->assertStatus(200)
                ->assertJson(['status' => 'success', 'message' => 'Weather data fetched from cache', 'data' => ['source' => 'cache']]);
    }
}
