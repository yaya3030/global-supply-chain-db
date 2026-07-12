<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExchangeRateController extends Controller
{
    protected $exchangeRateService;

    // Dependency Injection
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function getExchangeRate(Request $request): JsonResponse
    {
        // 1. Validasi parameter input
        $request->validate([
            'country_id' => 'required|exists:countries,id',
        ]);

        // 2. Ambil data negara dan mata uangnya dari database
        $country = Country::find($request->input('country_id'));
        $currencyCode = $country->currency_code;

        if (!$currencyCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kolom currency_code negara ini kosong di database.'
            ], 422);
        }

        // 3. Panggil Service (Default base memakai USD untuk standar rantai pasok global)
        $baseCurrency = $request->input('base_currency', 'USD');
        $rate = $this->exchangeRateService->getCurrentRate($currencyCode, $baseCurrency);

        // 4. Kirim respons JSON bersih
        return response()->json([
            'status' => 'success',
            'country' => $country->name,
            'base_currency' => $baseCurrency,
            'target_currency' => $currencyCode,
            'exchange_rate' => $rate,
            'last_updated' => now()->toDateTimeString()
        ], 200);
    }
}