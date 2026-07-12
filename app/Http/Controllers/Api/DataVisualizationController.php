<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataVisualizationService;
use Illuminate\Http\JsonResponse;

class DataVisualizationController extends Controller
{
    protected $visualizationService;

    public function __construct(DataVisualizationService $visualizationService)
    {
        $this->visualizationService = $visualizationService;
    }

    /**
     * Endpoint GET /api/data-visualization-metrics
     */
    public function getMetrics(): JsonResponse
    {
        $data = $this->visualizationService->getAggregatedMetrics();

        return response()->json([
            'status' => 'success',
            'engine' => 'Global Aggregation Visualization Engine',
            'calculated_at' => now()->toDateTimeString(),
            'payload' => $data
        ], 200);
    }
}