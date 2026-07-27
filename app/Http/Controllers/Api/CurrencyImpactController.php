<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CurrencyImpactService;
use Illuminate\Http\Request;
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

    /**
     * Endpoint GET /api/currency-historical?code=IDR
     * Returns 30-day simulated historical exchange rate data.
     */
    public function getHistoricalRates(Request $request): JsonResponse
    {
        $code = strtoupper($request->query('code', 'IDR'));
        $data = $this->currencyService->getHistoricalRates($code);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * Endpoint GET /api/currency-list
     * Returns all known currencies with their current rate vs USD.
     */
    public function getCurrencyList(): JsonResponse
    {
        $data = $this->currencyService->getCurrencyList();

        return response()->json([
            'status' => 'success',
            'base' => 'USD',
            'data' => $data
        ], 200);
    }
}