<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GlobalWeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GlobalWeatherController extends Controller
{
    protected $weatherService;

    public function __construct(GlobalWeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Endpoint GET /api/global-weather-status
     */
    public function getWeatherStatus(Request $request): JsonResponse
    {
        $countryId = $request->query('country_id') ? (int) $request->query('country_id') : null;
        $result = $this->weatherService->getMonitorData($countryId);

        $weatherList = $result['weather_list'];
        $countries = $result['countries'];

        // Summary counts per country
        $totalRain = count(array_filter($weatherList, fn($item) => $item['weather_type'] === 'rain'));
        $totalStorm = count(array_filter($weatherList, fn($item) => $item['weather_type'] === 'storm'));
        $totalStrongWind = count(array_filter($weatherList, fn($item) => $item['weather_type'] === 'strong_wind'));
        $totalClear = count(array_filter($weatherList, fn($item) => $item['weather_type'] === 'clear'));

        return response()->json([
            'status' => 'success',
            'system' => 'Global Country Weather Monitoring',
            'updated_at' => now()->toDateTimeString(),
            'countries_monitored' => count($weatherList),
            'summary' => [
                'total_rain' => $totalRain,
                'total_storm' => $totalStorm,
                'total_strong_wind' => $totalStrongWind,
                'total_clear' => $totalClear,
            ],
            'countries' => $countries,
            'data' => $weatherList
        ], 200);
    }
}