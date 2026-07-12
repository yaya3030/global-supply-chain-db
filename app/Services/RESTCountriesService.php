<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class RESTCountriesService
{
    /**
     * Fetch detailed country profile with Cache & Anti-Fail Fallback.
     */
    public function getCountryDetails(string $countryCode): ?array
    {
        $cleanCode = strtoupper(trim($countryCode));
        $cacheKey = "restcountries_" . strtolower($cleanCode);

        // 1. Jika ada di cache yang valid, ambil dari cache
        if (Cache::has($cacheKey) && Cache::get($cacheKey) !== null) {
            return Cache::get($cacheKey);
        }

        try {
            $url = "https://restcountries.com/v3.1/alpha/{$cleanCode}";
            $response = Http::withoutVerifying()->timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data) && isset($data[0])) {
                    Cache::put($cacheKey, $data[0], 86400); // Hanya cache jika sukses
                    return $data[0];
                }
            }
        } catch (Exception $e) {
            Log::error("REST Countries API Link Error: " . $e->getMessage());
        }

        // =======================================================
        // DATA CADANGAN (FALLBACK) - BIAR TIDAK AKAN PERNAH ERROR
        // =======================================================
        // Jika internet lokal memblokir API atau cache error, langsung pakai data ini:
        if ($cleanCode === 'ID' || $cleanCode === 'IDN') {
            $fallbackData = [
                'name' => ['official' => 'Republic of Indonesia'],
                'capital' => ['Jakarta'],
                'region' => 'Asia',
                'subregion' => 'South-Eastern Asia',
                'population' => 273523615,
                'flags' => ['png' => 'https://flagcdn.com/w320/id.png'],
                'maps' => ['googleMaps' => 'https://goo.gl/maps/bB7SVr4m4to4zU3v8'],
                'borders' => ['TLS', 'MYS', 'PNG']
            ];
            
            Cache::put($cacheKey, $fallbackData, 86400);
            return $fallbackData;
        }

        return null;
    }
}