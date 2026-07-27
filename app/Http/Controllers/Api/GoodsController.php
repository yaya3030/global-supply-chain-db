<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\GoodHistory;
use Illuminate\Http\JsonResponse;

class GoodsController extends Controller
{
    /**
     * Endpoint GET /api/port-goods/{port_id}
     */
    public function getGoodsByPort($portId): JsonResponse
    {
        $port = Port::find($portId);
        
        if (!$port) {
            return response()->json(['status' => 'error', 'message' => 'Port not found'], 404);
        }

        // Get goods currently at this port (their latest history record is at this port and they haven't departed)
        // Wait, the history table tracks arrivals and departures.
        // If they are currently at the port, their latest history entry should be for this port, and status should be arrived or delayed.
        
        $histories = GoodHistory::with('good')
            ->where('port_id', $portId)
            ->whereIn('status', ['arrived', 'delayed'])
            ->get();

        $goodsData = [];

        foreach ($histories as $history) {
            $good = $history->good;
            if (!$good) continue;

            // Check if this history is the latest for this good
            $latestHistory = GoodHistory::where('good_id', $good->id)->orderBy('arrival_time', 'desc')->first();
            
            if ($latestHistory && $latestHistory->id == $history->id) {
                // Good is currently here.
                
                // Fetch full route history for this good
                $fullRoute = GoodHistory::with('port')
                    ->where('good_id', $good->id)
                    ->orderBy('arrival_time', 'asc')
                    ->get()
                    ->map(function ($route) {
                        return [
                            'port_name' => $route->port->port_name,
                            'status' => $route->status,
                            'arrival_time' => $route->arrival_time,
                            'departure_time' => $route->departure_time,
                        ];
                    });

                $goodsData[] = [
                    'id' => $good->id,
                    'name' => $good->name,
                    'tracking_number' => $good->tracking_number,
                    'current_status' => $history->status,
                    'arrival_time' => $history->arrival_time,
                    'route_history' => $fullRoute
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'port_name' => $port->port_name,
            'total_goods' => count($goodsData),
            'goods' => $goodsData
        ]);
    }
}
