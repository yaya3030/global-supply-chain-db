<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RealtimePortService;
use App\Http\Middleware\ApiResponseWrapper;
use Illuminate\Http\Request;

/**
 * Realtime Port Data API Controller
 * Endpoint untuk mendapatkan real-time port & harbor information
 */
class RealtimePortController extends Controller
{
    protected $portService;

    public function __construct(RealtimePortService $portService)
    {
        $this->portService = $portService;
    }

    /**
     * GET /api/port/realtime
     * Get realtime port data untuk satu negara
     */
    public function getRealtime(Request $request)
    {
        $country = $request->query('country', 'ID');

        try {
            $portData = $this->portService->getPortData($country);
            return ApiResponseWrapper::success(
                $portData,
                'Real-time port data retrieved successfully'
            );
        } catch (\Exception $e) {
            report($e);
            return ApiResponseWrapper::error(
                'Failed to retrieve port data',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * GET /api/port/comparison
     * Compare port data untuk multiple negara
     */
    public function getComparison(Request $request)
    {
        $countries = $request->query('countries', 'ID,DE,CN,AU');
        $countryList = explode(',', $countries);

        try {
            $ports = $this->portService->getMultiplePortData($countryList);
            return ApiResponseWrapper::success(
                $ports,
                'Port comparison data retrieved successfully'
            );
        } catch (\Exception $e) {
            report($e);
            return ApiResponseWrapper::error(
                'Failed to retrieve port comparison',
                500
            );
        }
    }

    /**
     * POST /api/port/refresh
     * Force refresh port cache
     */
    public function refreshCache(Request $request)
    {
        $country = $request->input('country');

        try {
            $this->portService->clearCache($country);
            return ApiResponseWrapper::success(
                null,
                'Port cache refreshed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseWrapper::error(
                'Failed to refresh cache',
                500
            );
        }
    }
}
