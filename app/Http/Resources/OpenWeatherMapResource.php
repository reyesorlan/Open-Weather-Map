<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enum\OpenWeatherMapEnum;

class OpenWeatherMapResource extends JsonResource
{
    protected $source;
    public function __construct($resource, OpenWeatherMapEnum $source)
    {
        parent::__construct($resource);
        $this->source = $source;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'city' => $this['name'],
            'temperature' => $this['main']['temp'],
            'description' => $this['weather'][0]['description'],
            'timestamp' => $this['dt'],
            'source' => $this->source
        ];
    }
}
