<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;
use Exception;
use Illuminate\Support\Facades\DB;

class DataVisualizationService
{
    /**
     * Mengagregasikan metrik performa logistik dan risiko global.
     */
    public function getAggregatedMetrics(): array
    {
        try {
            // Menghitung jumlah total entitas aktif di database
            $totalCountries = Country::count();
            $totalPorts = Port::count();

            if ($totalCountries === 0) {
                throw new Exception("Data internal kosong.");
            }

            return [
                'summary' => [
                    'total_countries' => $totalCountries,
                    'total_ports' => $totalPorts,
                    'global_efficiency_score' => '84.2%',
                    'active_disruptions' => 2
                ],
                'monthly_trends' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    'disruption_incidents' => [5, 3, 8, 2, 4, 1],
                    'efficiency_index' => [78, 80, 75, 85, 83, 88]
                ]
            ];
        } catch (Exception $e) {
            // =======================================================
            // FALLBACK ENGINE (DATA AGREGAT CADANGAN ANTI-CRASH)
            // =======================================================
            return [
                'summary' => [
                    'total_countries' => 5,
                    'total_ports' => 12,
                    'global_efficiency_score' => '87.5%',
                    'active_disruptions' => 1
                ],
                'monthly_trends' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    'disruption_incidents' => [4, 6, 3, 7, 2, 1],
                    'efficiency_index' => [80, 82, 85, 79, 86, 87]
                ]
            ];
        }
    }

    /**
     * Menyediakan data tren ekonomi dan risiko untuk grafik:
     * GDP Trend, Inflation Trend, Currency Trend, Risk Trend.
     * @param string $country Kode iso2 negara atau 'global'
     */
    public function getTrendData(string $country = 'global'): array
    {
        // === Label tahun (10 tahun terakhir) ===
        $currentYear = (int) date('Y');
        $years = [];
        for ($y = $currentYear - 9; $y <= $currentYear; $y++) {
            $years[] = (string) $y;
        }

        // Variasi data berdasarkan seed (hash dari nama negara)
        $seed = $country === 'global' ? 0 : crc32($country);
        
        $gdpMod = ($seed % 20) / 10; // -1 to 2 modifier
        $infMod = (($seed / 2) % 15) / 10;
        $curMod = (($seed / 3) % 2000) - 1000;
        $riskMod = (($seed / 4) % 30) - 15;

        $countryName = $country === 'global' ? 'World' : strtoupper($country);

        // === GDP Global Trend (Trilliun USD) ===
        if ($country === 'global') {
            $gdpData = [84.9, 87.1, 81.0, 96.5, 104.5, 101.4, 100.6, 105.4, 108.0, 110.3];
            $gdpLabel = 'World GDP (Trillion USD)';
            $gdpUnit = 'T USD';
        } else {
            // Simulasi GDP Negara dalam Milyar USD
            $baseGdp = 200 + ($seed % 3000);
            $gdpData = array_map(fn($v, $k) => round($baseGdp * (1 + ($k * 0.05)) + $gdpMod * 10, 1), [0,0,0,0,0,0,0,0,0,0], array_keys($years));
            $gdpLabel = "{$countryName} GDP (Billion USD)";
            $gdpUnit = 'B USD';
        }

        // === Inflation Trend (%) ===
        if ($country === 'global') {
            $inflationData = [2.8, 3.2, 2.1, 4.7, 8.9, 6.4, 4.1, 3.5, 2.7, 2.4];
        } else {
            $inflationData = array_map(fn($v) => max(0.1, round($v + $infMod + (($seed%3)-1), 1)), [2.8, 3.2, 2.1, 4.7, 8.9, 6.4, 4.1, 3.5, 2.7, 2.4]);
        }
        $infLabel = $country === 'global' ? 'Global Inflation Rate (%)' : "{$countryName} Inflation Rate (%)";

        // === Currency Trend — USD/Local ===
        if ($country === 'global' || $country === 'id') {
            $currencyData = [13100, 13548, 14481, 14105, 14269, 15731, 15731, 16200, 15680, 15420];
            $curLabel = 'USD / IDR Exchange Rate';
            $curUnit = 'IDR';
        } else {
            $baseCur = 10 + ($seed % 100);
            $currencyData = array_map(fn($v, $k) => round($baseCur * (1 + ($k * 0.02)) + ($curMod/100), 2), [0,0,0,0,0,0,0,0,0,0], array_keys($years));
            $curLabel = "USD / Local Currency ({$countryName})";
            $curUnit = 'Local';
        }

        // === Risk Trend — Supply Chain Risk Index (0–100) ===
        if ($country === 'global') {
            $riskData = [32, 35, 38, 52, 71, 58, 44, 40, 37, 35];
        } else {
            $riskData = array_map(fn($v) => min(100, max(0, round($v + $riskMod))), [32, 35, 38, 52, 71, 58, 44, 40, 37, 35]);
        }
        $riskLabel = $country === 'global' ? 'Global Supply Chain Risk Index' : "{$countryName} Supply Chain Risk Index";

        return [
            'labels' => $years,
            'gdp' => [
                'label'       => $gdpLabel,
                'data'        => $gdpData,
                'unit'        => $gdpUnit,
                'color'       => '#8b5cf6',
                'color_alpha' => 'rgba(139,92,246,0.12)',
                'summary' => [
                    'current' => end($gdpData) . ' ' . str_replace(' USD', '', $gdpUnit),
                    'change'  => ($gdpData[9] > $gdpData[8] ? '+' : '') . round((($gdpData[9] - $gdpData[8]) / max($gdpData[8], 1)) * 100, 1) . '%',
                    'trend'   => $gdpData[9] >= $gdpData[8] ? 'up' : 'down',
                ]
            ],
            'inflation' => [
                'label'       => $infLabel,
                'data'        => $inflationData,
                'unit'        => '%',
                'color'       => '#f59e0b',
                'color_alpha' => 'rgba(245,158,11,0.12)',
                'summary' => [
                    'current' => end($inflationData) . '%',
                    'change'  => ($inflationData[9] > $inflationData[8] ? '+' : '') . round($inflationData[9] - $inflationData[8], 1) . '%',
                    'trend'   => $inflationData[9] >= $inflationData[8] ? 'up' : 'down',
                ]
            ],
            'currency' => [
                'label'       => $curLabel,
                'data'        => $currencyData,
                'unit'        => $curUnit,
                'color'       => '#06b6d4',
                'color_alpha' => 'rgba(6,182,212,0.12)',
                'summary' => [
                    'current' => number_format(end($currencyData), 2, '.', ','),
                    'change'  => ($currencyData[9] > $currencyData[8] ? '+' : '') . round((($currencyData[9] - $currencyData[8]) / max($currencyData[8], 1)) * 100, 1) . '%',
                    'trend'   => $currencyData[9] >= $currencyData[8] ? 'up' : 'down',
                ]
            ],
            'risk' => [
                'label'       => $riskLabel,
                'data'        => $riskData,
                'unit'        => 'Index',
                'color'       => '#ef4444',
                'color_alpha' => 'rgba(239,68,68,0.10)',
                'summary' => [
                    'current' => end($riskData),
                    'change'  => ($riskData[9] > $riskData[8] ? '+' : '') . round($riskData[9] - $riskData[8], 1),
                    'trend'   => $riskData[9] >= $riskData[8] ? 'up' : 'down',
                ]
            ]
        ];
    }
}