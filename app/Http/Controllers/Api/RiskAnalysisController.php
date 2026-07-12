<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SentimentAnalysisService;
use App\Services\RiskPredictionService;
use Illuminate\Http\Request;

class RiskAnalysisController extends Controller
{
    protected $sentimentSvc;
    protected $riskSvc;

    public function __construct(SentimentAnalysisService $s, RiskPredictionService $r)
    {
        $this->sentimentSvc = $s;
        $this->riskSvc = $r;
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'news_text' => 'required|string',
            'weather_risk' => 'required|numeric',
            'inflation_risk' => 'required|numeric',
            'currency_risk' => 'required|numeric'
        ]);

        // Proses
        $sentiment = $this->sentimentSvc->analyze($request->news_text);
        $totalRisk = $this->riskSvc->calculateGlobalRisk(
            $request->weather_risk,
            $request->inflation_risk,
            $sentiment,
            $request->currency_risk
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'sentiment_score' => $sentiment,
                'calculated_risk' => $totalRisk
            ]
        ]);
    }
}