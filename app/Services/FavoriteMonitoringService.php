<?php

namespace App\Services;

use App\Models\Port;
use Exception;

class FavoriteMonitoringService
{
    /**
     * Mengambil daftar status terkini dari negara yang difavoritkan.
     */
    public function getFavoriteStatus(): array
    {
        // Use a default user since there's no auth
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        $watchlists = \App\Models\Watchlist::with('country')->where('user_id', $user->id)->get();

        $list = [];
        foreach ($watchlists as $wl) {
            if (!$wl->country) continue;
            
            // Generate deterministic mock data
            $seed = crc32($wl->country->name);
            $riskScore = max(10, min(90, 30 + ($seed % 50)));
            
            if ($riskScore > 65) {
                $riskLevel = 'High';
                $status = 'Warning/Congestion';
            } elseif ($riskScore > 35) {
                $riskLevel = 'Medium';
                $status = 'Monitor Closely';
            } else {
                $riskLevel = 'Low';
                $status = 'Operational';
            }

            $list[] = [
                'id' => $wl->country->id,
                'name' => $wl->country->name,
                'iso2' => strtolower($wl->country->iso2),
                'status' => $status,
                'last_update' => now()->format('H:i'),
                'risk_level' => $riskLevel
            ];
        }

        return $list;
    }

    public function addFavorite($country_id)
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        \App\Models\Watchlist::firstOrCreate([
            'user_id' => $user->id,
            'country_id' => $country_id
        ]);
    }

    public function removeFavorite($country_id)
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password')]
        );

        \App\Models\Watchlist::where('user_id', $user->id)
            ->where('country_id', $country_id)
            ->delete();
    }
}