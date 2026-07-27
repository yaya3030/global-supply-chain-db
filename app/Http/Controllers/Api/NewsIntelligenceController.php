<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NewsIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NewsIntelligenceController extends Controller
{
    protected $newsService;

    public function __construct(NewsIntelligenceService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * GET /api/news-intelligence
     * Returns all categories combined (legacy endpoint).
     */
    public function getNewsAnalytics(): JsonResponse
    {
        $newsFeed = $this->newsService->analyzeLogisticsNews();

        $counts = ['Positive' => 0, 'Neutral' => 0, 'Disruption' => 0];
        foreach ($newsFeed as $item) {
            if (isset($counts[$item['impact_category']])) {
                $counts[$item['impact_category']]++;
            }
        }

        return response()->json([
            'status' => 'success',
            'engine' => 'AI News Intelligence Service',
            'generated_at' => now()->toDateTimeString(),
            'sentiment_distribution' => $counts,
            'articles' => $newsFeed
        ], 200);
    }

    /**
     * GET /api/news-by-category?category=logistics&country=Indonesia
     * Returns articles for a single category, optionally filtered by country.
     */
    public function getByCategory(Request $request): JsonResponse
    {
        $category = $request->query('category', 'logistics');
        $country  = $request->query('country', 'Global');

        $articles   = $this->newsService->fetchByCountryAndCategory($country, $category);
        $categories = $this->newsService->getCategories();

        return response()->json([
            'status'  => 'success',
            'category' => $category,
            'country'  => $country,
            'meta'     => $categories[$category] ?? [],
            'total'    => count($articles),
            'generated_at' => now()->toDateTimeString(),
            'articles' => $articles,
        ], 200);
    }

    /**
     * GET /api/news-categories
     * Returns list of available categories.
     */
    public function getCategories(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'categories' => $this->newsService->getCategories(),
        ], 200);
    }

    /**
     * GET /api/news-countries
     * Returns list of all countries for the dropdown.
     */
    public function getCountryList(): JsonResponse
    {
        return response()->json([
            'status'    => 'success',
            'countries' => $this->newsService->getCountryList(),
        ], 200);
    }
}