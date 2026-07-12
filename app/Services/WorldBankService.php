<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class WorldBankService
{
    /**
     * Fetch country indicator data from World Bank API with 1-hour caching.
     *
     * @param string $countryCode (ISO2 or ISO3 code, e.g., 'ID' or 'IDN')
     * @param string $indicator (e.g., 'NY.GDP.MKTP.CD' for GDP)
     * @return array|null
     */
    public function getCountryIndicator(string $countryCode, string $indicator = 'NY.GDP.MKTP.CD'): ?array
    {
        $cacheKey = "worldbank_" . strtolower($countryCode) . "_" . strtolower(str_replace('.', '_', $indicator));

        // Cache duration: 3600 seconds = 1 hour (Data ekonomi jarang berubah secara realtime)
        return Cache::remember($cacheKey, 3600, function () use ($countryCode, $indicator) {
            try {
                // World Bank API URL format
                $url = "https://api.worldbank.org/v2/country/{$countryCode}/indicator/{$indicator}";
                
                $response = Http::timeout(10)->get($url, [
                    'format' => 'json',
                    'per_page' => 10, // Mengambil 10 data tahun terakhir
                ]);

                if ($response->failed()) {
                    Log::error("World Bank API Error: Status code " . $response->status());
                    return null;
                }

                $data = $response->json();

                // World Bank API mengembalikan array ganda: index [0] adalah metadata, index [1] adalah data utama
                if (is_array($data) && count($data) > 1 && is_array($data[1])) {
                    return $data[1]; 
                }

                Log::error("World Bank API Error: Invalid or empty response structure.");
                return null;

            } catch (Exception $e) {
                Log::error("Failed to connect to World Bank API: " . $e->getMessage());
                return null;
            }
        });
    }
}