<?php

namespace App\Services;

use App\Models\Country;
use Exception;

class GlobalWeatherService
{
    /**
     * Peta koordinat geografis akurat untuk negara-negara di dunia.
     */
    private array $countryCoordinates = [
        'ID' => [-0.7893, 113.9213], // Indonesia
        'SG' => [1.3521, 103.8198],   // Singapore
        'CN' => [35.8617, 104.1954],  // China
        'US' => [37.0902, -95.7129],  // United States
        'DE' => [51.1657, 10.4515],   // Germany
        'NL' => [52.1326, 5.2913],    // Netherlands
        'JP' => [36.2048, 138.2529],  // Japan
        'AU' => [-25.2744, 133.7751], // Australia
        'GB' => [55.3781, -3.4360],   // United Kingdom
        'FR' => [46.2276, 2.2137],    // France
        'BR' => [-14.2350, -51.9253], // Brazil
        'IN' => [20.5937, 78.9629],   // India
        'RU' => [61.5240, 105.3188],  // Russia
        'CA' => [56.1304, -106.3468], // Canada
        'KR' => [35.9078, 127.7669],  // South Korea
        'IT' => [41.8719, 12.5674],   // Italy
        'ES' => [40.4637, -3.7492],   // Spain
        'TH' => [15.8700, 100.9925],  // Thailand
        'MY' => [4.2105, 101.9758],   // Malaysia
        'VN' => [14.0583, 108.2772],  // Vietnam
        'PH' => [12.8797, 121.7740],  // Philippines
        'SA' => [23.8859, 45.0792],   // Saudi Arabia
        'AE' => [23.4241, 53.8478],   // United Arab Emirates
        'EG' => [26.8206, 30.8025],   // Egypt
        'MX' => [23.6345, -102.5528], // Mexico
        'AR' => [-38.4161, -63.6167], // Argentina
        'TR' => [38.9637, 35.2433],   // Turkey
        'ZA' => [-30.5595, 22.9375],  // South Africa
        'NZ' => [-40.9006, 174.8860], // New Zealand
        'HK' => [22.3193, 114.1694],  // Hong Kong
        'CZ' => [49.8175, 15.4730],   // Czech Republic
        'GP' => [16.2650, -61.5510],  // Guadeloupe
        'KI' => [1.8709, -157.3630],  // Kiribati
        'DJ' => [11.8251, 42.5903],   // Djibouti
        'PT' => [39.3999, -8.2245],   // Portugal
        'ER' => [15.1794, 39.7823],   // Eritrea
        'YE' => [15.5527, 48.5164],   // Yemen
        'GR' => [39.0742, 21.8243],   // Greece
        'SE' => [60.1282, 18.6435],   // Sweden
        'NO' => [60.4720, 8.4689],    // Norway
        'FI' => [61.9241, 25.7482],   // Finland
        'PL' => [51.9194, 19.1451],   // Poland
        'BE' => [50.5039, 4.4699],    // Belgium
        'CH' => [46.8182, 8.2275],    // Switzerland
        'AT' => [47.5162, 14.5501],   // Austria
        'DK' => [56.2639, 9.5018],    // Denmark
        'IE' => [53.4129, -8.2439],   // Ireland
        'CL' => [-35.6751, -71.5430], // Chile
        'CO' => [4.5709, -74.2973],   // Colombia
        'PE' => [-9.1900, -75.0152],  // Peru
        'SG' => [1.3521, 103.8198],   // Singapore
    ];

    private array $nameCoordinates = [
        'Indonesia' => [-0.7893, 113.9213],
        'Singapore' => [1.3521, 103.8198],
        'China' => [35.8617, 104.1954],
        'United States' => [37.0902, -95.7129],
        'Germany' => [51.1657, 10.4515],
        'Netherlands' => [52.1326, 5.2913],
        'Japan' => [36.2048, 138.2529],
        'Australia' => [-25.2744, 133.7751],
        'United Kingdom' => [55.3781, -3.4360],
        'France' => [46.2276, 2.2137],
        'Brazil' => [-14.2350, -51.9253],
        'India' => [20.5937, 78.9629],
        'Russia' => [61.5240, 105.3188],
        'Canada' => [56.1304, -106.3468],
        'South Korea' => [35.9078, 127.7669],
        'Italy' => [41.8719, 12.5674],
        'Spain' => [40.4637, -3.7492],
        'Thailand' => [15.8700, 100.9925],
        'Malaysia' => [4.2105, 101.9758],
        'Vietnam' => [14.0583, 108.2772],
        'Philippines' => [12.8797, 121.7740],
        'Saudi Arabia' => [23.8859, 45.0792],
        'United Arab Emirates' => [23.4241, 53.8478],
        'Egypt' => [26.8206, 30.8025],
        'Mexico' => [23.6345, -102.5528],
        'Argentina' => [-38.4161, -63.6167],
        'Turkey' => [38.9637, 35.2433],
        'South Africa' => [-30.5595, 22.9375],
        'New Zealand' => [-40.9006, 174.8860],
        'Hong Kong' => [22.3193, 114.1694],
        'Czech Republic' => [49.8175, 15.4730],
        'Guadeloupe' => [16.2650, -61.5510],
        'Kiribati' => [1.8709, -157.3630],
        'Djibouti' => [11.8251, 42.5903],
        'Portugal' => [39.3999, -8.2245],
        'Eritrea' => [15.1794, 39.7823],
        'Yemen' => [15.5527, 48.5164],
    ];

    /**
     * Memproses data cuaca berbasis NEGARA dengan koordinat geografis akurat.
     */
    public function getMonitorData(?int $countryId = null): array
    {
        $countryWeatherList = [];
        $uniqueCountriesMap = [];

        try {
            $query = Country::with('ports');
            if ($countryId) {
                $query->where('id', $countryId);
            }
            $countries = $query->get();

            foreach ($countries as $country) {
                // Clean country name
                $cleanName = trim(preg_replace('/\s+[A-Z]{2}$/', '', $country->name));

                if (isset($uniqueCountriesMap[$cleanName]) && !$countryId) {
                    continue;
                }
                $uniqueCountriesMap[$cleanName] = true;

                // Determine accurate latitude and longitude
                $iso = strtoupper($country->iso2 ?? '');
                $coords = $this->countryCoordinates[$iso] ?? ($this->nameCoordinates[$cleanName] ?? null);

                if ($coords) {
                    $lat = $coords[0];
                    $lng = $coords[1];
                } else {
                    $lat = $country->ports && $country->ports->count() > 0 ? (float)$country->ports->avg('latitude') : 10.0;
                    $lng = $country->ports && $country->ports->count() > 0 ? (float)$country->ports->avg('longitude') : 20.0;
                }

                // Deterministic weather seed based on country ID
                $seed = abs((int) ($country->id * 37 + abs($lat) * 10));
                $modulo = $seed % 10;

                if ($modulo <= 2) {
                    $weatherType = 'storm';
                    $condition = 'Badai Tropis & Petir (Severe Storm)';
                    $temp = 24 + ($seed % 4);
                    $windSpeed = 30 + ($seed % 18);
                    $visibility = 'Sangat Buruk (1-2 KM)';
                    $safetyStatus = 'Alert';
                } elseif ($modulo <= 5) {
                    $weatherType = 'rain';
                    $condition = 'Hujan Lebat (Heavy Rain)';
                    $temp = 25 + ($seed % 4);
                    $windSpeed = 16 + ($seed % 10);
                    $visibility = 'Sedang (4-6 KM)';
                    $safetyStatus = 'Warning';
                } elseif ($modulo <= 7) {
                    $weatherType = 'strong_wind';
                    $condition = 'Angin Kencang (Strong Wind)';
                    $temp = 26 + ($seed % 5);
                    $windSpeed = 25 + ($seed % 12);
                    $visibility = 'Buruk (3-5 KM)';
                    $safetyStatus = 'Warning';
                } else {
                    $weatherType = 'clear';
                    $condition = 'Cerah & Berawan (Clear Sky)';
                    $temp = 28 + ($seed % 5);
                    $windSpeed = 8 + ($seed % 8);
                    $visibility = 'Sangat Baik (10 KM)';
                    $safetyStatus = 'Safe';
                }

                $countryWeatherList[] = [
                    'country_id' => $country->id,
                    'country_name' => $cleanName,
                    'iso2' => $country->iso2,
                    'iso3' => $country->iso3,
                    'region' => $country->region ?? 'Global',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'weather_type' => $weatherType,
                    'condition' => $condition,
                    'temperature' => $temp . '°C',
                    'wind_speed' => $windSpeed . ' Knots',
                    'wind_speed_val' => $windSpeed,
                    'visibility' => $visibility,
                    'safety_status' => $safetyStatus
                ];
            }

            // Get complete unique list of countries for dropdown select sorted alphabetically
            $allCountriesSelect = Country::orderBy('name', 'asc')->get()->map(function($c) {
                return [
                    'id' => $c->id,
                    'name' => trim(preg_replace('/\s+[A-Z]{2}$/', '', $c->name)),
                    'iso2' => $c->iso2
                ];
            })->unique('name')->values()->toArray();

        } catch (Exception $e) {
            $countryWeatherList = [
                [
                    'country_id' => 1,
                    'country_name' => 'Indonesia',
                    'iso2' => 'ID',
                    'iso3' => 'IDN',
                    'region' => 'Southeast Asia',
                    'latitude' => -0.7893,
                    'longitude' => 113.9213,
                    'weather_type' => 'rain',
                    'condition' => 'Hujan Lebat (Heavy Rain)',
                    'temperature' => '28°C',
                    'wind_speed' => '18 Knots',
                    'visibility' => 'Sedang (5 KM)',
                    'safety_status' => 'Warning'
                ]
            ];

            $allCountriesSelect = [
                ['id' => 1, 'name' => 'Indonesia', 'iso2' => 'ID']
            ];
        }

        return [
            'weather_list' => $countryWeatherList,
            'countries' => $allCountriesSelect
        ];
    }
}