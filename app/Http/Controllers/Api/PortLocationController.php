<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PortLocationService;
use Illuminate\Http\JsonResponse;

class PortLocationController extends Controller
{
    protected $locationService;

    public function __construct(PortLocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Endpoint GET /api/port-locations
     */
    public function getLocations(): JsonResponse
    {
        $data = $this->locationService->getGeospatialLocations();

        return response()->json([
            'status' => 'success',
            'system' => 'Geospatial Port Location Intelligence',
            'total_nodes' => count($data),
            'results' => $data
        ], 200);
    }
}