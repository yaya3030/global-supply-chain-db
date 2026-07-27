<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;

class ImportAnalyzerService
{
    /**
     * Calculate 5 pillar logistics risk
     */
    public function calculateRisk($originCountryId, $originPortId, $destCountryId, $destPortId)
    {
        $originCountry = Country::find($originCountryId);
        $destCountry = Country::find($destCountryId);
        $originPort = Port::find($originPortId);
        $destPort = Port::find($destPortId);

        // Deterministic random seeds based on IDs to simulate API data consistency
        $seedO = crc32($originCountry->name ?? 'A');
        $seedD = crc32($destCountry->name ?? 'B');
        $seedOP = crc32($originPort->port_name ?? 'X');
        $seedDP = crc32($destPort->port_name ?? 'Y');

        // 1. Weather Risk (Dampak cuaca buruk pada pelabuhan asal & tujuan)
        $originWeatherRisk = 10 + ($seedOP % 40); // 10-50
        $destWeatherRisk = 10 + ($seedDP % 40);
        $avgWeather = round(($originWeatherRisk + $destWeatherRisk) / 2);
        
        $weatherDetail = "Kondisi operasional normal.";
        if ($avgWeather > 35) {
            $weatherDetail = "Cuaca buruk dapat mengganggu pengiriman, potensi badai tercatat.";
        } elseif ($avgWeather > 25) {
            $weatherDetail = "Angin kencang moderat terpantau di jalur pengiriman.";
        }

        // 2. Exchange Rate Risk (Fluktuasi nilai tukar)
        // If same currency (e.g. EUR to EUR), risk is 0. Else calculate.
        $currencyRisk = ($originCountry->currency_code === $destCountry->currency_code) ? 5 : 20 + (($seedO + $seedD) % 60);
        
        $currencyDetail = "Mata uang stabil, volatilitas rendah.";
        if ($currencyRisk > 60) {
            $currencyDetail = "Nilai tukar mata uang berubah signifikan, berdampak pada harga akhir.";
        } elseif ($currencyRisk > 40) {
            $currencyDetail = "Tren fluktuasi moderat antara {$originCountry->currency_code} dan {$destCountry->currency_code}.";
        }

        // 3. Geopolitics Risk (Risiko negara konflik/keamanan)
        $originGeoRisk = 15 + ($seedO % 50);
        $destGeoRisk = 15 + ($seedD % 50);
        $geoRisk = max($originGeoRisk, $destGeoRisk); // Geopolitics relies on the weakest link
        
        $geoDetail = "Hubungan diplomatik dan jalur dagang aman.";
        if ($geoRisk > 55) {
            $geoDetail = "Konflik geopolitik meningkatkan risiko embargo atau penundaan.";
        } elseif ($geoRisk > 35) {
            $geoDetail = "Ketegangan regional dapat mempengaruhi prioritas kargo.";
        }

        // 4. Port Congestion Risk (Kemacetan Pelabuhan)
        $originCongestion = 20 + ($seedOP % 60);
        $destCongestion = 20 + ($seedDP % 60);
        $congestionRisk = round(($originCongestion + $destCongestion) / 2);
        
        $congestionDetail = "Lalu lintas pelabuhan terpantau lancar.";
        if ($congestionRisk > 60) {
            $congestionDetail = "Kemacetan pelabuhan menyebabkan keterlambatan sandar dan bongkar muat.";
        } elseif ($congestionRisk > 40) {
            $congestionDetail = "Antrian kapal di atas ambang normal.";
        }

        // 5. Inflation Risk (Inflasi yang mempengaruhi biaya produksi)
        $inflationRisk = 15 + ($seedO % 50); // Mainly relies on exporter country
        
        $inflationDetail = "Tingkat inflasi eksportir terkendali.";
        if ($inflationRisk > 50) {
            $inflationDetail = "Inflasi suatu negara mempengaruhi biaya produksi dan bahan baku secara tajam.";
        } elseif ($inflationRisk > 30) {
            $inflationDetail = "Kenaikan moderat pada indeks harga produsen ekspor.";
        }

        // Total Score Calculation (Weight: 20% each)
        $totalRiskScore = round(
            ($avgWeather * 0.2) + 
            ($currencyRisk * 0.2) + 
            ($geoRisk * 0.2) + 
            ($congestionRisk * 0.2) + 
            ($inflationRisk * 0.2)
        );

        $recommendation = "Lanjutkan Impor. Kondisi secara umum sangat kondusif.";
        if ($totalRiskScore >= 60) {
            $recommendation = "Tunda atau Cari Alternatif. Risiko agregat sangat tinggi, potensi kerugian operasional besar.";
        } elseif ($totalRiskScore >= 40) {
            $recommendation = "Lanjutkan dengan Hati-hati. Pertimbangkan asuransi kargo ekstra atau lindung nilai mata uang (hedging).";
        }

        return [
            'origin' => [
                'country' => $originCountry->name ?? 'Unknown',
                'port' => $originPort->port_name ?? 'Unknown',
                'currency' => $originCountry->currency_code ?? 'USD'
            ],
            'destination' => [
                'country' => $destCountry->name ?? 'Unknown',
                'port' => $destPort->port_name ?? 'Unknown',
                'currency' => $destCountry->currency_code ?? 'USD'
            ],
            'pillars' => [
                'weather' => ['score' => $avgWeather, 'desc' => $weatherDetail],
                'currency' => ['score' => $currencyRisk, 'desc' => $currencyDetail],
                'geopolitics' => ['score' => $geoRisk, 'desc' => $geoDetail],
                'congestion' => ['score' => $congestionRisk, 'desc' => $congestionDetail],
                'inflation' => ['score' => $inflationRisk, 'desc' => $inflationDetail],
            ],
            'total_score' => $totalRiskScore,
            'recommendation' => $recommendation
        ];
    }
}
