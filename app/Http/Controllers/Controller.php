<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: "OpenWeatherMap API Documentation", version: "1.0")]
#[OA\Server(url: "http://localhost:8000/api", description: "Localhost")]
#[OA\SecurityScheme(
    securityScheme: "apiKey",
    type: "apiKey",
    description: "Use API Key",
    name: "X-API-KEY",
    in: "header"
)]
#[OA\Tag(name: "OpenWeatherMap")]


abstract class Controller
{
    public function success($data, $message = 'Success', $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function error($message = 'Error', $statusCode = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
