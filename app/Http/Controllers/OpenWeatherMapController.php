<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherMapService;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class OpenWeatherMapController extends Controller
{
    protected $weatherService;

    public function __construct(OpenWeatherMapService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    #[OA\Get(
        path: "/v1/openweathermap/test-api-key",
        summary: "Test API Key",
        security: [["apiKey" => []]],
        tags: ["OpenWeatherMap"],
        responses: [
            new OA\Response(
                response: 200,
                description: "API key is valid",
                content: new OA\JsonContent(
                    example: [
                        "status" => "success",
                        "message" => "API key is valid",
                        "data" => null
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid API key",
                content: new OA\JsonContent(
                    example: [
                        "status" => "error",
                        "message" => "Invalid API key"
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid API key. Requires to provide a valid API key in the request header.",
                content: new OA\JsonContent(
                    example: [
                        "status" => "error",
                        "message" => "Invalid API key",
                    ]
                )
            ),
        ],
    )]

    public function testApiKey()
    {
        if ($this->weatherService->testApiKey()) {
            return $this->success(null, "API key is valid");
        }

        return $this->error('Invalid API key');
    }

    #[OA\Get(
        path: "/v1/openweathermap/weather/{city}",
        summary: "Get Current Weather From External API",
        security: [["apiKey" => []]],
        tags: ["OpenWeatherMap"],
        parameters: [
            new OA\Parameter(
                name: "city",
                in: "path",
                description: "City name",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Current weather data",
                content: new OA\JsonContent(
                    example: [
                        "status" => "success",
                        "message" => "Weather data fetched successfully",
                        "data" => [
                            "city" => "",
                            "temperature" => 0,
                            "description" => "",
                            "timestamp" => "",
                            "source" => "external"
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Unable to fetch weather data",
                content: new OA\JsonContent(
                    example: [
                        "status" => "error",
                        "message" => "Unable to fetch weather data",
                        "data" => null
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid API key. Requires to provide a valid API key in the request header.",
                content: new OA\JsonContent(
                    example: [
                        "status" => "error",
                        "message" => "Invalid API key",
                    ]
                )
            ),
        ],
    )]

    public function currentWeather(Request $request, $city = 'London')
    {
        $weatherData = $this->weatherService->getCurrentWeather($city);

        if ($weatherData) {
            return $this->success($weatherData, "Weather data fetched successfully");
        }

        return $this->error('Unable to fetch weather data');
    }

    #[OA\Get(
        path: "/v1/openweathermap/weather/{city}/cached",
        summary: "Get Current Weather From Cache",
        security: [["apiKey" => []]],
        tags: ["OpenWeatherMap"],
        parameters: [
            new OA\Parameter(
                name: "city",
                in: "path",
                description: "City name",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Get Current Weather From Cache",
                content: new OA\JsonContent(
                    example: [
                        "status" => "success",
                        "message" => "Weather data fetched from cache",
                        "data" => [
                            "city" => "",
                            "temperature" => 0,
                            "description" => "",
                            "timestamp" => "",
                            "source" => "cache"
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "No cached weather data available",
                content: new OA\JsonContent(
                    example: [
                        "status" => "error",
                        "message" => "No cached weather data available",
                        "data" => null
                    ]
                )
            ),
        ],
    )]
    public function cachedWeather(Request $request, $city = 'London')
    {
        $weatherData = $this->weatherService->getCachedWeather($city);

        if ($weatherData) {
            return $this->success($weatherData, "Weather data fetched from cache");
        }

        return $this->error('No cached weather data available');
    }
}
