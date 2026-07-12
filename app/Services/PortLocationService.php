<?php

namespace App\Services;

use App\Models\Port;
use Exception;

class PortLocationService
{
    /**
     * Mengambil seluruh data koordinat geografis pelabuhan global.
     */
    public function getGeospatialLocations(): array
    {
        $locations = [];

        try {
            // Query database untuk mengambil data pelabuhan beserta relasi negaranya
            $ports = Port::with('country')->get();

            if ($ports->isEmpty()) {
                throw new Exception("Database koordinat pelabuhan belum terisi.");
            }

            foreach ($ports as $port) {
                $locations[] = [
                    'port_name' => $port->port_name,
                    'country_name' => $port->country->name ?? 'Global Hub',
                    'latitude' => (float) $port->latitude,
                    'longitude' => (float) $port->longitude,
                ];
            }
        } catch (Exception $e) {
            // =======================================================
            // FALLBACK ENGINE (DATA GEOSPATIAL CADANGAN)
            // =======================================================
            $locations = [
                [
                    'port_name' => 'Tanjung Priok (Jakarta)',
                    'country_name' => 'Indonesia',
                    'latitude' => -6.1033,
                    'longitude' => 106.8792
                ],
                [
                    'port_name' => 'Port of Singapore',
                    'country_name' => 'Singapore',
                    'latitude' => 1.2740,
                    'longitude' => 103.8010
                ],
                [
                    'port_name' => 'Port of Rotterdam',
                    'country_name' => 'Netherlands',
                    'latitude' => 51.9244,
                    'longitude' => 4.4777
                ],
                [
                    'port_name' => 'Port of Los Angeles',
                    'country_name' => 'United States',
                    'latitude' => 33.7420,
                    'longitude' => -118.2673
                ]
            ];
        }

        return $locations;
    }
}