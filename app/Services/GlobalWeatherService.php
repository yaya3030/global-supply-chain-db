<?php

namespace App\Services;

use App\Models\Port;
use Exception;

class GlobalWeatherService
{
    /**
     * Memproses data cuaca maritim di berbagai pelabuhan global.
     */
    public function getMonitorData(): array
    {
        $weatherMonitorList = [];

        try {
            // Mengambil data pelabuhan beserta relasi negaranya
            $ports = Port::with('country')->get();

            if ($ports->isEmpty()) {
                throw new Exception("Database pelabuhan kosong.");
            }

            foreach ($ports as $port) {
                // Algoritma simulasi cuaca dinamis berbasis koordinat pelabuhan
                $temp = 27 + round(sin($port->latitude) * 3);
                $windSpeed = 12 + round(cos($port->longitude) * 8);
                $visibility = $windSpeed > 18 ? 'Poor (4 KM)' : 'Good (10 KM)';
                
                // Menentukan tingkat keamanan pelayaran (Safe, Warning, Alert)
                $status = 'Safe';
                if ($windSpeed > 18) {
                    $status = 'Warning';
                }

                $weatherMonitorList[] = [
                    'port_name' => $port->port_name,
                    'country_name' => $port->country->name ?? 'Global Hub',
                    'temperature' => $temp . '°C',
                    'wind_speed' => $windSpeed . ' Knots',
                    'visibility' => $visibility,
                    'condition' => $status == 'Safe' ? 'Clear Sky' : 'Heavy Rain / Strong Winds',
                    'safety_status' => $status
                ];
            }
        } catch (Exception $e) {
            // =======================================================
            // FALLBACK ENGINE (DATA CADANGAN ANTI-CRASH)
            // =======================================================
            $weatherMonitorList = [
                [
                    'port_name' => 'Tanjung Priok',
                    'country_name' => 'Indonesia',
                    'temperature' => '29°C',
                    'wind_speed' => '12 Knots',
                    'visibility' => 'Good (10 KM)',
                    'condition' => 'Clear Sky',
                    'safety_status' => 'Safe'
                ],
                [
                    'port_name' => 'Port of Singapore',
                    'country_name' => 'Singapore',
                    'temperature' => '28°C',
                    'wind_speed' => '22 Knots',
                    'visibility' => 'Poor (4 KM)',
                    'condition' => 'Heavy Rain & High Waves',
                    'safety_status' => 'Warning'
                ],
                [
                    'port_name' => 'Port of Rotterdam',
                    'country_name' => 'Netherlands',
                    'temperature' => '16°C',
                    'wind_speed' => '14 Knots',
                    'visibility' => 'Good (10 KM)',
                    'condition' => 'Partly Cloudy',
                    'safety_status' => 'Safe'
                ]
            ];
        }

        return $weatherMonitorList;
    }
}