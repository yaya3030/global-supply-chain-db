<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RealtimeCurrencyService;
use App\Http\Middleware\ApiResponseWrapper;
use Illuminate\Http\Request;

/**
 * Realtime Currency API Controller
 * Endpoint untuk mendapatkan real-time currency exchange rates
 */
class RealtimeCurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(RealtimeCurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * GET /api/currency/realtime
     * Get realtime exchange rate untuk satu negara
     */
    public function getRealtime(Request $request)
    {
        $country = $request->query('country', 'ID');
        $baseCurrency = $request->query('base', 'USD');

        try {
            $rate = $this->currencyService->getExchangeRate($country, $baseCurrency);
            return ApiResponseWrapper::success(
                $rate,
                'Real-time exchange rate retrieved successfully'
            );
        } catch (\Exception $e) {
            report($e);
            return ApiResponseWrapper::error(
                'Failed to retrieve exchange rate',
                500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * GET /api/currency/trend
     * Get exchange rate trend/perubahan
     */
    public function getTrend(Request $request)
    {
        $currency = $request->query('currency', 'IDR');
        $period = $request->query('period', 'day'); // day, week, month

        try {
            $trend = $this->currencyService->getRateChangeTrend($currency, $period);
            return ApiResponseWrapper::success(
                $trend,
                'Exchange rate trend retrieved successfully'
            );
        } catch (\Exception $e) {
            report($e);
            return ApiResponseWrapper::error(
                'Failed to retrieve exchange rate trend',
                500
            );
        }
    }

    /**
     * GET /api/currency/comparison
     * Compare exchange rates untuk multiple negara
     */
    public function getComparison(Request $request)
    {
        $countries = $request->query('countries', 'ID,MY,SG,TH');
        $baseCurrency = $request->query('base', 'USD');

        try {
            $countryList = explode(',', str_replace(' ', '', $countries));
            $rates = $this->currencyService->getMultipleRates($countryList, $baseCurrency);

            return ApiResponseWrapper::success(
                [
                    'base_currency' => $baseCurrency,
                    'rates' => $rates,
                    'compared_countries' => $countryList
                ],
                'Exchange rate comparison retrieved successfully'
            );
        } catch (\Exception $e) {
            report($e);
            return ApiResponseWrapper::error(
                'Failed to retrieve exchange rate comparison',
                500
            );
        }
    }

    /**
     * POST /api/currency/refresh
     * Force refresh currency cache
     */
    public function refreshCache(Request $request)
    {
        try {
            $currency = $request->input('currency', null);
            $this->currencyService->clearCache($currency);

            return ApiResponseWrapper::success(
                ['refreshed_at' => now()->toIso8601String()],
                'Currency cache refreshed successfully'
            );
        } catch (\Exception $e) {
            report($e);
            return ApiResponseWrapper::error(
                'Failed to refresh cache',
                500
            );
        }
    }
}
