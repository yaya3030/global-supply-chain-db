<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Port;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    protected $weatherService;

    // Dependency Injection of our WeatherService
    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getWeather(Request $request): JsonResponse
    {
        // 1. Validation: Either country_id or both latitude & longitude must be provided
        if (!$request->has('country_id') && (!$request->has('latitude') || !$request->has('longitude'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing parameters. Please provide either country_id or latitude & longitude.'
            ], 400);
        }

        // 2. Scenario A: Fetch weather based on direct Latitude and Longitude input
        if ($request->has('latitude') && $request->has('longitude')) {
            $lat = (float) $request->input('latitude');
            $lon = (float) $request->input('longitude');

            $weather = $this->weatherService->getWeather($lat, $lon);

            if (!$weather) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Weather service is currently unavailable. Please try again later.'
                ], 502);
            }

            return response()->json([
                'status' => 'success',
                'mode' => 'coordinates',
                'data' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current_weather' => $weather
                ]
            ], 200);
        }

        // 3. Scenario B: Fetch weather for all Ports within a specified Country ID
        $countryId = $request->input('country_id');
        $country = Country::find($countryId);

        if (!$country) {
            return response()->json([
                'status' => 'error',
                'message' => 'Country not found.'
            ], 404);
        }

        // Get all ports for this country (using the Seeder data from previous steps)
        $ports = Port::where('country_id', $countryId)->get();
        $portsWeatherData = [];

        foreach ($ports as $port) {
            $weather = $this->weatherService->getWeather((float)$port->latitude, (float)$port->longitude);
            
            $portsWeatherData[] = [
                'port_id' => $port->id,
                'port_name' => $port->port_name,
                'port_code' => $port->port_code,
                'latitude' => $port->latitude,
                'longitude' => $port->longitude,
                'current_weather' => $weather ?? 'Data Unavailable'
            ];
        }

        return response()->json([
            'status' => 'success',
            'mode' => 'country_ports',
            'country_name' => $country->name,
            'data' => $portsWeatherData
        ], 200);
    }
}