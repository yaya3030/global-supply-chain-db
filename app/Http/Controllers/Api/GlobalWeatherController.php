<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GlobalWeatherService;
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
    public function getWeatherStatus(): JsonResponse
    {
        $data = $this->weatherService->getMonitorData();

        return response()->json([
            'status' => 'success',
            'system' => 'Global Maritime Weather Monitoring',
            'updated_at' => now()->toDateTimeString(),
            'ports_monitored' => count($data),
            'data' => $data
        ], 200);
    }
}