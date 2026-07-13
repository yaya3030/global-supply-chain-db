<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Realtime Currency Exchange Service
 * Menyediakan data exchange rate real-time dengan caching optimal
 */
class RealtimeCurrencyService
{
    /**
     * Cache duration untuk currency rates (5 menit = realtime yang cukup)
     */
    protected const CACHE_DURATION = 300; // 5 minutes

    /**
     * API Provider: exchangerate-api.com (free tier tersedia)
     * Alternative: fixer.io, openexchangerates.org
     */
    protected $apiProvider = 'exchangerate-api.com';
    protected $apiKey; // Set dari config

    public function __construct()
    {
        $this->apiKey = config('services.exchange_rate_api_key', '');
    }

    /**
     * Get realtime exchange rate untuk suatu negara
     */
    public function getExchangeRate($countryCode = 'US', $baseCurrency = 'USD')
    {
        $cacheKey = "currency_rate_{$baseCurrency}_{$countryCode}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($countryCode, $baseCurrency) {
            return $this->fetchExchangeRateFromApi($baseCurrency, $countryCode);
        });
    }

    /**
     * Get multiple exchange rates (untuk country comparison)
     */
    public function getMultipleRates($countries = [], $baseCurrency = 'USD')
    {
        $rates = [];
        foreach ($countries as $country) {
            $rates[$country] = $this->getExchangeRate($country, $baseCurrency);
        }
        return $rates;
    }

    /**
     * Get rate change trend (percentage change dalam periode tertentu)
     */
    public function getRateChangeTrend($currency = 'USD', $period = 'day')
    {
        $cacheKey = "currency_trend_{$currency}_{$period}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($currency, $period) {
            // Simulasi perubahan rate untuk demo
            // Dalam produksi, gunakan historical data dari API
            return [
                'currency' => $currency,
                'current_rate' => rand(15000, 16000) / 100, // IDR example
                'previous_rate' => rand(14500, 15500) / 100,
                'change_percent' => rand(-5, 5) / 10,
                'direction' => rand(0, 1) === 0 ? 'up' : 'down',
                'period' => $period,
                'updated_at' => now()->toIso8601String()
            ];
        });
    }

    /**
     * Fetch dari API eksternal
     */
    protected function fetchExchangeRateFromApi($baseCurrency, $targetCountry)
    {
        try {
            // Gunakan ER-API yang fresh & realtime
            $response = Http::timeout(5)->get(
                "https://open.er-api.com/v6/latest/{$baseCurrency}"
            );

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];
                
                // Map country code ke currency code
                $currencyCode = $this->getCurrencyCode($targetCountry);
                $rate = $rates[$currencyCode] ?? null;

                if ($rate) {
                    // Get yesterday's rate untuk calculate change
                    $yesterdayKey = "fx_yesterday_{$currencyCode}";
                    $yesterdayRate = Cache::get($yesterdayKey, $rate);
                    Cache::put($yesterdayKey, $rate, now()->addDay());

                    $changePercent = $yesterdayRate != 0
                        ? (($rate - $yesterdayRate) / $yesterdayRate) * 100
                        : 0;

                    return [
                        'base_currency' => $baseCurrency,
                        'target_country' => $targetCountry,
                        'currency_code' => $currencyCode,
                        'rate' => round($rate, 2),
                        'rate_change_percent' => round($changePercent, 2),
                        'direction' => $changePercent >= 0 ? 'up' : 'down',
                        'timestamp' => now()->toIso8601String(),
                        'provider' => 'open.er-api.com'
                    ];
                }
            }

            return $this->getDefaultRate($baseCurrency, $targetCountry);

        } catch (\Exception $e) {
            report($e);
            return $this->getDefaultRate($baseCurrency, $targetCountry);
        }
    }

    /**
     * Map country code to currency code
     */
    protected function getCurrencyCode($countryCode)
    {
        $map = [
            'DE' => 'EUR',
            'CN' => 'CNY',
            'ID' => 'IDR',
            'AU' => 'AUD',
            'MY' => 'MYR',
            'SG' => 'SGD',
            'TH' => 'THB',
            'PH' => 'PHP',
            'VN' => 'VND',
        ];
        return $map[$countryCode] ?? 'USD';
    }

    /**
     * Default rate jika API error
     */
    protected function getDefaultRate($baseCurrency, $targetCountry)
    {
        $defaultRates = [
            'ID' => 15500,  // IDR
            'MY' => 4.50,   // MYR
            'SG' => 1.35,   // SGD
            'TH' => 35.50,  // THB
            'PH' => 57.50,  // PHP
            'VN' => 24500,  // VND
            'DE' => 0.92,   // EUR
            'CN' => 7.20,   // CNY
            'AU' => 1.53,   // AUD
        ];

        $rate = $defaultRates[$targetCountry] ?? 1.0;
        $currencyCode = $this->getCurrencyCode($targetCountry);

        return [
            'base_currency' => $baseCurrency,
            'target_country' => $targetCountry,
            'currency_code' => $currencyCode,
            'rate' => $rate,
            'rate_change_percent' => rand(-5, 5) / 10,
            'direction' => rand(0, 1) === 0 ? 'up' : 'down',
            'timestamp' => now()->toIso8601String(),
            'provider' => 'default',
            'note' => 'Using default rates due to API unavailability'
        ];
    }

    /**
     * Clear cache untuk force refresh
     */
    public function clearCache($currency = null)
    {
        if ($currency) {
            Cache::forget("currency_rate_{$currency}");
        } else {
            Cache::flush();
        }
    }
}
