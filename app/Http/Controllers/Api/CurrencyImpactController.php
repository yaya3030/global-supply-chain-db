<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyImpactService;
use Illuminate\Http\JsonResponse;

class CurrencyImpactController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyImpactService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Endpoint GET /api/currency-impact-analysis
     */
    public function getImpactAnalysis(): JsonResponse
    {
        $data = $this->currencyService->calculateCurrencyImpacts();

        return response()->json([
            'status' => 'success',
            'analysis_type' => 'Supply Chain Currency Impact Metrics',
            'generated_at' => now()->toDateTimeString(),
            'results' => $data
        ], 200);
    }
}