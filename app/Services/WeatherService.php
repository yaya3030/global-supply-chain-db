<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class WeatherService
{
    public function getWeather(float $latitude, float $longitude): ?array
    {
        $latKey = round($latitude, 2);
        $lonKey = round($longitude, 2);
        $cacheKey = "weather_data_lat_{$latKey}_lon_{$lonKey}";

        return Cache::remember($cacheKey, 1800, function () use ($latitude, $longitude) {
            try {
                $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,rain,showers,snowfall,wind_speed_10m,weather_code',
                    'timezone' => 'auto'
                ]);

                if ($response->failed()) {
                    Log::error("Open-Meteo API Error: Status code " . $response->status());
                    return null;
                }

                $data = $response->json();

                if (!isset($data['current'])) {
                    Log::error("Open-Meteo API Error: Invalid response structure.");
                    return null;
                }

                return $data['current'];

            } catch (Exception $e) {
                Log::error("Failed to connect to Open-Meteo API: " . $e->getMessage());
                return null;
            }
        });
    }
}