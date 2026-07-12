<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\RESTCountriesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RESTCountriesController extends Controller
{
    protected $countriesService;

    // Dependency Injection
    public function __construct(RESTCountriesService $countriesService)
    {
        $this->countriesService = $countriesService;
    }

    public function getCountryDetails(Request $request): JsonResponse
    {
        // 1. Validasi parameter input
        $request->validate([
            'country_id' => 'required|exists:countries,id',
        ]);

        // 2. Cari negara di database untuk mendapatkan kode ISO
        $country = Country::find($request->input('country_id'));
        $code = $country->iso2 ?? $country->iso3;

        if (!$code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Country ISO code is missing in database.'
            ], 422);
        }

        // 3. Panggil Service
        $apiData = $this->countriesService->getCountryDetails($code);

        if (!$apiData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data from REST Countries API.'
            ], 502);
        }

        // 4. Saring data penting untuk Supply Chain & Dashboard
        $formattedData = [
            'country_name' => $country->name,
            'official_name' => $apiData['name']['official'] ?? null,
            'capital' => $apiData['capital'][0] ?? 'N/A',
            'region' => $apiData['region'] ?? null,
            'subregion' => $apiData['subregion'] ?? null,
            'population' => $apiData['population'] ?? 0,
            'flag_url' => $apiData['flags']['png'] ?? null,
            'google_maps' => $apiData['maps']['googleMaps'] ?? null,
            'borders' => $apiData['borders'] ?? [], // Kode negara tetangga untuk jalur darat
        ];

        return response()->json([
            'status' => 'success',
            'data' => $formattedData
        ], 200);
    }
}