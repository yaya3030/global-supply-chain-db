<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CountryComparisonService;
use Illuminate\Http\JsonResponse;

class CountryComparisonController extends Controller
{
    protected $comparisonService;

    public function __construct(CountryComparisonService $comparisonService)
    {
        $this->comparisonService = $comparisonService;
    }

    /**
     * Endpoint GET /api/country-comparison-data
     */
    public function getComparisonMetrics(): JsonResponse
    {
        $data = $this->comparisonService->getComparisonData();

        return response()->json([
            'status' => 'success',
            'engine' => 'Cross-Country Comparison Analytics Engine',
            'generated_at' => now()->toDateTimeString(),
            'results' => $data
        ], 200);
    }
}