<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FavoriteMonitoringService;
use Illuminate\Http\JsonResponse;

class FavoriteMonitoringController extends Controller
{
    protected $favService;

    public function __construct(FavoriteMonitoringService $favService)
    {
        $this->favService = $favService;
    }

    /**
     * Endpoint GET /api/favorite-monitoring
     */
    public function getFavorites(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->favService->getFavoriteStatus()
        ], 200);
    }

    public function addFavorite(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate(['country_id' => 'required|integer']);
        $this->favService->addFavorite($request->country_id);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Country added to favorites'
        ], 200);
    }

    public function removeFavorite($country_id): JsonResponse
    {
        $this->favService->removeFavorite($country_id);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Country removed from favorites'
        ], 200);
    }
}