<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ShippingRouteController extends Controller
{
    /**
     * Endpoint GET /api/shipping-routes
     * Returns a list of simulated global shipping routes connecting major ports.
     */
    public function getRoutes(): JsonResponse
    {
        // Realistic Maritime Shipping Routes Data with Precise Waypoints
        $routes = [
            // Indonesia to Singapore (Intra-Asia) - Smooth
            [
                'route_id' => 'R-001',
                'origin' => ['name' => 'Tanjung Priok', 'lat' => -6.1033, 'lng' => 106.8792],
                'destination' => ['name' => 'Port of Singapore', 'lat' => 1.2740, 'lng' => 103.8010],
                'status' => 'smooth',
                'vessel' => 'Cosco Shipping',
                'waypoints' => [
                    [-6.1033, 106.8792],
                    [-3.0, 106.5], // Java Sea
                    [1.2740, 103.8010]
                ]
            ],
            // Singapore to Europe (via Suez) - Weather Delay
            [
                'route_id' => 'R-002',
                'origin' => ['name' => 'Port of Singapore', 'lat' => 1.2740, 'lng' => 103.8010],
                'destination' => ['name' => 'Port of Rotterdam', 'lat' => 51.9244, 'lng' => 4.4777],
                'status' => 'weather_delay',
                'vessel' => 'Ever Given',
                'waypoints' => [
                    [1.2740, 103.8010],
                    [5.0, 97.0],   // Strait of Malacca
                    [5.0, 80.0],   // South of Sri Lanka
                    [10.0, 60.0],  // Arabian Sea
                    [12.0, 48.0],  // Gulf of Aden
                    [12.5, 43.3],  // Bab el Mandeb Strait
                    [20.0, 39.0],  // Red Sea
                    [28.0, 33.5],  // Gulf of Suez
                    [29.9, 32.5],  // Suez Canal South
                    [31.3, 32.3],  // Suez Canal North (Port Said) - Prevents clipping Egypt
                    [34.0, 25.0],  // Mediterranean Sea
                    [38.0, 10.0],  // North of Tunisia
                    [35.9, -5.5],  // Strait of Gibraltar
                    [36.9, -9.1],  // Cape St Vincent (Portugal)
                    [43.0, -9.5],  // Finisterre (Spain)
                    [49.5, -3.0],  // English Channel
                    [51.9244, 4.4777]
                ]
            ],
            // Rotterdam to Jakarta (via Cape of Good Hope) - Economic Delay
            // Replaced Transpacific route to avoid Leaflet Antimeridian wrapping issues
            [
                'route_id' => 'R-003',
                'origin' => ['name' => 'Port of Rotterdam', 'lat' => 51.9244, 'lng' => 4.4777],
                'destination' => ['name' => 'Tanjung Priok', 'lat' => -6.1033, 'lng' => 106.8792],
                'status' => 'economic_delay',
                'vessel' => 'MSC Isabella',
                'waypoints' => [
                    [51.9244, 4.4777],
                    [49.5, -3.0],  // English Channel
                    [43.0, -9.5],  // Coast of Spain
                    [28.0, -16.0], // Canary Islands
                    [0.0, -10.0],  // Equatorial Atlantic
                    [-35.0, 20.0], // Cape of Good Hope (South Africa)
                    [-25.0, 60.0], // Indian Ocean
                    [-6.5, 105.0], // Sunda Strait
                    [-6.1033, 106.8792]
                ]
            ],
            // Los Angeles to Rotterdam (via Panama Canal) - Smooth
            [
                'route_id' => 'R-004',
                'origin' => ['name' => 'Port of Los Angeles', 'lat' => 33.7420, 'lng' => -118.2673],
                'destination' => ['name' => 'Port of Rotterdam', 'lat' => 51.9244, 'lng' => 4.4777],
                'status' => 'smooth',
                'vessel' => 'Maersk Mc-Kinney',
                'waypoints' => [
                    [33.7420, -118.2673],
                    [25.0, -115.0], // Off coast of Baja
                    [15.0, -100.0], // Off coast of Mexico
                    [12.0, -92.0],  // Off coast of Guatemala
                    [7.5, -80.0],   // Gulf of Panama (Pacific side)
                    [8.9, -79.5],   // Panama Canal South
                    [9.3, -79.9],   // Panama Canal North (Caribbean side)
                    [15.0, -75.0],  // Caribbean Sea
                    [18.5, -68.0],  // Mona Passage
                    [25.0, -60.0],  // North Atlantic
                    [50.0, -10.0],  // South of Ireland
                    [49.5, -3.0],   // English Channel
                    [51.9244, 4.4777]
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'total_routes' => count($routes),
            'results' => $routes
        ]);
    }
}
