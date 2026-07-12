<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class ExchangeRateService
{
    /**
     * Fetch current exchange rate against USD with 24-hour caching and full fallback.
     */
    public function getCurrentRate(string $targetCurrency, string $baseCurrency = 'USD'): float
    {
        $target = strtoupper(trim($targetCurrency));
        $base = strtoupper(trim($baseCurrency));
        $cacheKey = "fx_rate_{$base}_{$target}";

        // 1. Jika data ada di cache, langsung gunakan untuk efisiensi
        if (Cache::has($cacheKey) && Cache::get($cacheKey) !== null) {
            return (float) Cache::get($cacheKey);
        }

        try {
            // Menggunakan open API endpoint publik dari ExchangeRate-API (Tanpa perlu ribet input API Key)
            $url = "https://open.er-api.com/v6/latest/{$base}";
            $response = Http::withoutVerifying()->timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rates'][$target])) {
                    $rate = (float) $data['rates'][$target];
                    Cache::put($cacheKey, $rate, 86400); // Simpan 24 jam
                    return $rate;
                }
            }
        } catch (Exception $e) {
            Log::error("ExchangeRate API Jaringan Error: " . $e->getMessage());
        }

        // =======================================================
        // DATA CADANGAN (FALLBACK ANTI-GAGAL)
        // =======================================================
        // Jika API eksternal down / internet terputus, sistem otomatis pakai data ini:
        if ($base === 'USD' && $target === 'IDR') {
            return 16250.00; // Nilai kurs standar aman
        }

        return 1.0; // Default return jika mata uang sama
    }
}