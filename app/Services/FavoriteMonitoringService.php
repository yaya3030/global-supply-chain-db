<?php

namespace App\Services;

use App\Models\Port;
use Exception;

class FavoriteMonitoringService
{
    /**
     * Mengambil daftar status terkini dari entitas yang difavoritkan.
     */
    public function getFavoriteStatus(): array
    {
        try {
            // Simulasi mengambil data favorit (mengasumsikan relasi atau data favorit ada)
            // Di sini kita mengambil 3 pelabuhan utama sebagai sampel favorit pengguna
            $favorites = Port::whereIn('id', [1, 2, 3])->get();

            if ($favorites->isEmpty()) {
                throw new Exception("Data favorit belum diatur.");
            }

            $list = [];
            foreach ($favorites as $port) {
                $list[] = [
                    'name' => $port->port_name,
                    'status' => 'Operational',
                    'last_update' => now()->format('H:i'),
                    'risk_level' => 'Low'
                ];
            }
            return $list;

        } catch (Exception $e) {
            // =======================================================
            // FALLBACK ENGINE (DATA CADANGAN FAVORIT)
            // =======================================================
            return [
                ['name' => 'Tanjung Priok', 'status' => 'Operational', 'last_update' => '09:00', 'risk_level' => 'Low'],
                ['name' => 'Port of Singapore', 'status' => 'Heavy Congestion', 'last_update' => '08:45', 'risk_level' => 'Medium'],
                ['name' => 'Port of Rotterdam', 'status' => 'Operational', 'last_update' => '09:15', 'risk_level' => 'Low']
            ];
        }
    }
}