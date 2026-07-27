<?php

namespace App\Services;

use App\Models\Country;
use Exception;

class CountryComparisonService
{
    /**
     * Memproses data komparatif performa dan risiko antar negara secara dinamis.
     * @param string $c1 Kode ISO2 negara 1 (default: DE)
     * @param string $c2 Kode ISO2 negara 2 (default: AU)
     */
    public function getComparisonData(string $c1 = 'DE', string $c2 = 'AU'): array
    {
        $country1 = Country::where('iso2', $c1)->first() ?? Country::first();
        $country2 = Country::where('iso2', $c2)->first() ?? Country::orderBy('id', 'desc')->first();

        // Helper to generate consistent deterministic fake data based on country name
        $generateData = function($country) {
            $seed = crc32($country->name);
            
            // Generate pseudo-random realistic values
            $gdp = max(0.1, round((1000 + ($seed % 4000)) / 1000, 2)); // 0.1 to 5.0 Trillion
            $inflation = max(0.5, round(2 + (($seed % 150) - 75) / 10, 1)); // -5.5 to 9.5 %
            $risk = max(10, min(90, 30 + ($seed % 50))); // 10 to 90 index
            $weather = max(1, min(10, 2 + ($seed % 8))); // 1 to 10
            $exchangeRate = max(0.5, round(10 + (($seed % 15000) - 7500) / 100, 2)); // Currency to USD
            
            // Some manual capital assignments for realism if matched, else fake
            $capitals = ['DE' => 'Berlin', 'AU' => 'Canberra', 'US' => 'Washington D.C.', 'ID' => 'Jakarta', 'JP' => 'Tokyo', 'CN' => 'Beijing', 'SG' => 'Singapore', 'GB' => 'London'];
            $capital = $capitals[$country->iso2] ?? ($country->name . ' City');
            
            $population = max(2, round(10 + ($seed % 200), 1)); // 2 to 210 Million
            $efficiency = max(40, min(100, 60 + ($seed % 40))); // 40 to 100

            return [
                'name' => $country->name,
                'iso2' => $country->iso2,
                'currency' => $country->currency_code ?? 'USD',
                'capital' => $capital,
                'population' => $population . ' M',
                'data' => [
                    'gdp' => $gdp,
                    'inflation' => $inflation,
                    'risk' => $risk,
                    'weather' => $weather,
                    'exchange' => $exchangeRate,
                    'efficiency' => $efficiency
                ],
                'raw_data' => [$gdp, $inflation, $risk, $weather, $exchangeRate, $efficiency]
            ];
        };

        $comparisonData = [
            'metrics' => [
                'GDP (Trillion USD)', 
                'Inflasi (%)', 
                'Skor Risiko (0-100)', 
                'Suhu/Cuaca (Disruption 0-10)', 
                'Nilai Tukar (vs USD)',
                'Nilai Pasok (Efficiency 0-100)'
            ],
            'countries' => [
                $generateData($country1),
                $generateData($country2)
            ]
        ];

        return $comparisonData;
    }
}