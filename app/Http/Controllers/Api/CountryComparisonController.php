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
    public function getComparisonMetrics(\Illuminate\Http\Request $request): JsonResponse
    {
        $country1 = $request->query('country1', 'DE');
        $country2 = $request->query('country2', 'AU');

        $data = $this->comparisonService->getComparisonData($country1, $country2);

        return response()->json([
            'status' => 'success',
            'engine' => 'Cross-Country Comparison Analytics Engine',
            'generated_at' => now()->toDateTimeString(),
            'results' => $data
        ], 200);
    }
}