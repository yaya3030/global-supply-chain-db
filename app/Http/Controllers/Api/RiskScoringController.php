<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RiskScoringService;
use Illuminate\Http\JsonResponse;

class RiskScoringController extends Controller
{
    protected $riskService;

    public function __construct(RiskScoringService $riskService)
    {
        $this->riskService = $riskService;
    }

    /**
     * Endpoint GET /api/risk-scoring
     */
    public function getRiskScores(): JsonResponse
    {
        $data = $this->riskService->calculateGlobalRisks();

        return response()->json([
            'status' => 'success',
            'engine' => 'V2 Risk Scoring System',
            'calculated_at' => now()->toDateTimeString(),
            'results' => $data
        ], 200);
    }
}