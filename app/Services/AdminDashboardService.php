<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class AdminDashboardService
{
    public function getSystemStats(): array
    {
        try {
            // Contoh Query Database untuk statistik sistem
            $totalUsers = DB::table('users')->count();
            $totalIntegrations = 8; // Simulai jumlah modul API aktif

            return [
                'summary' => [
                    'active_users' => $totalUsers,
                    'system_health' => '98.5%',
                    'api_load' => '42ms',
                    'active_modules' => $totalIntegrations
                ],
                'traffic_data' => [65, 59, 80, 81, 56, 55, 40] // Data per jam
            ];
        } catch (Exception $e) {
            // FALLBACK ENGINE: Data simulasi jika DB error
            return [
                'summary' => [
                    'active_users' => 1250,
                    'system_health' => '99.9%',
                    'api_load' => '32ms',
                    'active_modules' => 8
                ],
                'traffic_data' => [40, 50, 70, 90, 60, 45, 85]
            ];
        }
    }
}