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
}