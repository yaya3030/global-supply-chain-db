<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsIntelligenceService;
use Illuminate\Http\JsonResponse;

class NewsIntelligenceController extends Controller
{
    protected $newsService;

    public function __construct(NewsIntelligenceService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Endpoint GET /api/news-intelligence
     */
    public function getNewsAnalytics(): JsonResponse
    {
        $newsFeed = $this->newsService->analyzeLogisticsNews();

        // Hitung total agregat distribusi sentimen untuk kebutuhan Chart.js
        $counts = ['Positive' => 0, 'Neutral' => 0, 'Disruption' => 0];
        foreach ($newsFeed as $item) {
            $counts[$item['impact_category']]++;
        }

        return response()->json([
            'status' => 'success',
            'engine' => 'AI News Intelligence Service',
            'generated_at' => now()->toDateTimeString(),
            'sentiment_distribution' => $counts,
            'articles' => $newsFeed
        ], 200);
    }
}