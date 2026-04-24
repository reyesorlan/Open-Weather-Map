# Open Weather Map

## Requirements
- PHP ^8.3 (as per project composer.json)
- Composer
- Laravel (project is already scaffolded)


## 📦 Project Setup

### 1. Environment configuration
Copy the example environment file:

```bash
cp .env.example .env
cp .env.example .env.testing
```

Update .env and .env.testing with your own API keys:
```
API_KEY=your_api_key_here
OPEN_WEATHER_API_KEY=your_openweather_api_key_here
(You may use the provided example or generate new using command `openssl rand -base64 32` )
```
### 2. Install dependencies
```bash
composer install
```

### 3. Generate application key
```bash
php artisan key:generate
```

### 4. Run migrations
```bash
php artisan migrate
```

### 5. Run tests
```bash
php artisan test --filter OpenWeatherMapTest
```
See tests at OpenWeatherMapTest.php

### 6. Start the server
```bash
php artisan serve
```

### 7. Test your Open Weather Map API key
Use curl to call the endpoint:
```bash
curl -H "X-API-KEY: your_api_key_here" \
     http://localhost:8000/api/test-api-key
```

### 8. Test the weather API
Use curl to call the endpoint, replacing {city} with your target city:
```bash
curl -H "X-API-KEY: your_api_key_here" \
     http://localhost:8000/api/weather/{city}
```
### 🗂 Notes

All endpoints expect the API token header used by the project middleware (see routes/api.php).

## Usage / Endpoints
### Validate API key:
- GET /v1/openweathermap/test-api-key
- GET /test-api-key
- Handler
  - App\Http\Controllers\OpenWeatherMapController::testApiKey
### Fetch current weather (external):
- GET /v1/openweathermap/weather/{city}
- GET /weather/{city}
- Handler
  - App\Http\Controllers\OpenWeatherMapController::currentWeather
### Fetch cached weather:
- GET /v1/openweathermap/weather/{city}/cached
- GET /weather/{city}/cached
- Handler
  - App\Http\Controllers\OpenWeatherMapController::cachedWeather


## Files of interest
- Controller: OpenWeatherMapController.php
- Service: OpenWeatherMapService.php
- Resource: OpenWeatherMapResource.php
- Routes: api.php
- Tests: OpenWeatherMapTest.php
- Config: openweathermap.php
