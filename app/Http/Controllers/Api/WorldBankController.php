<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\WorldBankService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorldBankController extends Controller
{
    protected $worldBankService;

    // Dependency Injection
    public function __construct(WorldBankService $worldBankService)
    {
        $this->worldBankService = $worldBankService;
    }

    public function getEconomicIndicators(Request $request): JsonResponse
    {
        // 1. Validasi parameter input
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'indicator' => 'nullable|string'
        ]);

        // 2. Cari negara di database untuk mendapatkan kode ISO
        $country = Country::find($request->input('country_id'));
        
        // Gunakan parameter indicator dari request, atau default ke GDP ('NY.GDP.MKTP.CD')
        $indicator = $request->input('indicator', 'NY.GDP.MKTP.CD');

        // 3. Panggil Service menggunakan kode iso2 (atau iso3) negara tersebut
        $code = $country->iso2 ?? $country->iso3;
        
        if (!$code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Country ISO code is missing in database.'
            ], 422);
        }

        $apiData = $this->worldBankService->getCountryIndicator($code, $indicator);

        if (!$apiData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data from World Bank API. Please try again later.'
            ], 502);
        }

        // 4. Format ulang response agar bersih dan mudah dibaca di Frontend
        $formattedData = collect($apiData)->map(function ($item) {
            return [
                'year' => $item['date'],
                'value' => $item['value'],
                'decimal_match' => $item['decimal']
            ];
        })->filter(fn($item) => !is_null($item['value']))->values(); // Filter data tahun yang kosong

        return response()->json([
            'status' => 'success',
            'country' => $country->name,
            'indicator_code' => $indicator,
            'data' => $formattedData
        ], 200);
    }
}