<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected array $defaultCountries = ['Germany', 'China', 'Indonesia', 'Australia', 'Singapore', 'United States'];

    protected array $countryCodes = [
        'Germany'       => ['iso2' => 'DE', 'iso3' => 'DEU', 'currency' => 'EUR', 'lat' => 51.1657, 'lng' => 10.4515],
        'China'         => ['iso2' => 'CN', 'iso3' => 'CHN', 'currency' => 'CNY', 'lat' => 35.8617, 'lng' => 104.1954],
        'Indonesia'     => ['iso2' => 'ID', 'iso3' => 'IDN', 'currency' => 'IDR', 'lat' => -0.7893, 'lng' => 113.9213],
        'Australia'     => ['iso2' => 'AU', 'iso3' => 'AUS', 'currency' => 'AUD', 'lat' => -25.2744, 'lng' => 133.7751],
        'Singapore'     => ['iso2' => 'SG', 'iso3' => 'SGP', 'currency' => 'SGD', 'lat' => 1.3521, 'lng' => 103.8198],
        'United States' => ['iso2' => 'US', 'iso3' => 'USA', 'currency' => 'USD', 'lat' => 37.0902, 'lng' => -95.7129],
    ];

    public function index()
    {
        $dbCountries = DB::table('countries')->orderBy('name')->pluck('name')->toArray();
        $allCountries = !empty($dbCountries) ? $dbCountries : $this->defaultCountries;

        return view('dashboard', [
            'countries'    => $allCountries,
            'allCountries' => $allCountries,
        ]);
    }

    public function countryData(Request $request)
    {
        $country = $request->query('country', 'Germany');
        $meta = $this->countryCodes[$country] ?? null;

        if (!$meta) {
            // Fallback: try to find in the database
            $dbCountry = DB::table('countries')->where('name', $country)->first();
            if ($dbCountry) {
                $port = DB::table('ports')->where('country_id', $dbCountry->id)->first();
                if ($port && $port->latitude && $port->longitude) {
                    $lat = (float) $port->latitude;
                    $lng = (float) $port->longitude;
                } else {
                    $seed = crc32($dbCountry->iso2);
                    $lat = (($seed % 1400) / 10.0) - 70;
                    $lng = ((abs($seed) % 3600) / 10.0) - 180;
                }
                $meta = [
                    'iso2'     => $dbCountry->iso2,
                    'iso3'     => $dbCountry->iso3,
                    'currency' => $dbCountry->currency_code,
                    'lat'      => round($lat, 4),
                    'lng'      => round($lng, 4),
                ];
            } else {
                return response()->json(['message' => 'Negara tidak dikenal'], 404);
            }
        }

        $cacheKey = 'dashboard_country_' . $meta['iso2'];

        $result = Cache::get($cacheKey);

        if (!$result || $result['population'] === '-') {
            $result = [
                'population' => $this->getPopulation($meta['iso3']),
                'gdp'        => $this->getGdp($meta['iso3']),
                'inflation'  => $this->getInflation($meta['iso3']),
                'currency'   => $this->getCurrency($meta['currency']),
                'weather'    => $this->getWeather($meta['lat'], $meta['lng']),
                'risk'       => $this->getRiskScore($country),
                'trend'      => $this->getRiskTrend($country),
                'news'       => $this->getNews($country),
            ];

            // Cache for 10 minutes only if successful. If failed, cache for 10 seconds to prevent spam.
            if ($result['population'] !== '-' || $result['currency']['display'] !== '-') {
                Cache::put($cacheKey, $result, now()->addMinutes(10));
            } else {
                Cache::put($cacheKey, $result, now()->addSeconds(10));
            }
        }

        return response()->json($result);
    }

    protected function getPopulation(string $iso3): string
    {
        try {
            $response = Http::timeout(2)->get(
                "https://api.worldbank.org/v2/country/{$iso3}/indicator/SP.POP.TOTL",
                ['format' => 'json', 'per_page' => 1, 'mrnev' => 1]
            );

            $value = $response->json()[1][0]['value'] ?? null;

            if ($value) return $this->formatBigNumber($value);
        } catch (\Throwable $e) {}

        $dummies = [
            'DEU' => '83.2M', 'CHN' => '1.4B', 'IDN' => '273.5M', 'AUS' => '25.6M',
            'SGP' => '5.4M', 'USA' => '331.9M'
        ];
        return $dummies[$iso3] ?? '10M';
    }

    protected function getGdp(string $iso3): string
    {
        try {
            $response = Http::timeout(2)->get(
                "https://api.worldbank.org/v2/country/{$iso3}/indicator/NY.GDP.MKTP.CD",
                ['format' => 'json', 'per_page' => 1, 'mrnev' => 1]
            );

            $value = $response->json()[1][0]['value'] ?? null;

            if ($value) return '$' . $this->formatBigNumber($value, true);
        } catch (\Throwable $e) {}

        $dummies = [
            'DEU' => '$4.2T', 'CHN' => '$17.7T', 'IDN' => '$1.1T', 'AUS' => '$1.5T',
            'SGP' => '$397B', 'USA' => '$23.3T'
        ];
        return $dummies[$iso3] ?? '$500B';
    }

    protected function getInflation(string $iso3): string
    {
        try {
            $response = Http::timeout(2)->get(
                "https://api.worldbank.org/v2/country/{$iso3}/indicator/FP.CPI.TOTL.ZG",
                ['format' => 'json', 'per_page' => 1, 'mrnev' => 1]
            );

            $value = $response->json()[1][0]['value'] ?? null;

            if ($value !== null) return round($value, 1) . '%';
        } catch (\Throwable $e) {}

        $dummies = [
            'DEU' => '3.1%', 'CHN' => '0.9%', 'IDN' => '1.5%', 'AUS' => '2.8%',
            'SGP' => '2.1%', 'USA' => '4.7%'
        ];
        return $dummies[$iso3] ?? '2.0%';
    }

    protected function getCurrency(string $currencyCode): array
    {
        try {
            $response = Http::timeout(5)->get(
                "https://open.er-api.com/v6/latest/USD"
            );

            $rates = $response->json()['rates'] ?? [];
            $currentRate = $rates[$currencyCode] ?? null;

            if (!$currentRate) {
                return [
                    'rate' => '0.0',
                    'rate_change_percent' => '0.0',
                    'direction' => 'up',
                    'display' => '-'
                ];
            }

            $yesterdayKey = 'fx_yesterday_' . $currencyCode;
            $yesterdayRate = Cache::get($yesterdayKey);
            
            // Jika tidak ada history, ambil dari cache dengan simulasi minor variance
            if (!$yesterdayRate) {
                $yesterdayRate = $currentRate * (rand(98, 102) / 100); // Simulated variance 98-102%
                Cache::put($yesterdayKey, $yesterdayRate, now()->addDay());
            } else {
                // Update dengan realtime variance untuk demo effect
                $variance = (rand(-3, 3) / 1000); // Tiny variance 0.3%
                Cache::put($yesterdayKey, $currentRate * (1 + $variance), now()->addDay());
            }

            $changePercent = $yesterdayRate != 0
                ? (($currentRate - $yesterdayRate) / $yesterdayRate) * 100
                : 0;

            // Format display value sesuai kurs (jika > 1000, gunakan K format)
            $displayRate = $currentRate >= 1000
                ? ($currentRate >= 1000000 ? round($currentRate / 1000000, 2) . 'M' : round($currentRate / 1000, 1) . 'K')
                : number_format($currentRate, 2);

            return [
                'rate' => number_format($currentRate, 2),
                'rate_change_percent' => number_format($changePercent, 2),
                'direction' => $changePercent >= 0 ? 'up' : 'down',
                'display' => $displayRate . ' ' . $currencyCode . '/USD'
            ];
        } catch (\Throwable $e) {
            report($e);
            return [
                'rate' => '0.0',
                'rate_change_percent' => '0.0',
                'direction' => 'up',
                'display' => '-'
            ];
        }
    }

    protected function getWeather(float $lat, float $lng): array
    {
        try {
            $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lng,
                'current_weather' => true,
            ]);

            $current = $response->json()['current_weather'] ?? null;

            if (!$current) {
                return ['temp' => '-', 'condition' => '-', 'code' => null, 'lat' => $lat, 'lng' => $lng];
            }

            $code = $current['weathercode'] ?? null;

            return [
                'temp' => round($current['temperature']),
                'condition' => $this->weatherCodeToLabel($code),
                'code' => $code,
                'lat' => $lat,
                'lng' => $lng,
            ];
        } catch (\Throwable $e) {
            report($e);
            return ['temp' => '-', 'condition' => '-', 'code' => null, 'lat' => $lat, 'lng' => $lng];
        }
    }

    protected function weatherCodeToLabel(?int $code): string
    {
        if ($code === null) return '-';

        return match (true) {
            $code === 0 => 'Cerah',
            in_array($code, [1, 2, 3]) => 'Berawan',
            in_array($code, [45, 48]) => 'Berkabut',
            $code >= 51 && $code <= 67 => 'Hujan ringan',
            $code >= 71 && $code <= 77 => 'Bersalju',
            $code >= 80 && $code <= 82 => 'Hujan sedang',
            $code >= 95 && $code <= 99 => 'Badai petir',
            default => 'Tidak diketahui',
        };
    }

    protected function getRiskScore(string $country): array
    {
        $dummyScores = [
            'Germany' => 22,
            'China' => 47,
            'Indonesia' => 19,
            'Australia' => 25,
        ];

        $score = $dummyScores[$country] ?? 30;
        $level = $score < 30 ? 'rendah' : ($score < 60 ? 'sedang' : 'tinggi');

        return ['score' => $score, 'level' => $level];
    }

    protected function getRiskTrend(string $country): array
    {
        $dummyTrends = [
            'Germany'   => [8, 12, 10, 15, 22],
            'China'     => [20, 28, 35, 40, 47],
            'Indonesia' => [15, 14, 17, 16, 19],
            'Australia' => [18, 20, 19, 23, 25],
        ];

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            'values' => $dummyTrends[$country] ?? [10, 10, 10, 10, 10],
        ];
    }

    protected function getNews(string $country): array
    {
        $apiKey = config('services.gnews.key');

        if ($apiKey) {
            try {
                $response = Http::timeout(5)->get('https://gnews.io/api/v4/search', [
                    'q' => $country . ' trade OR shipping OR economy',
                    'lang' => 'en',
                    'max' => 3,
                    'apikey' => $apiKey,
                ]);

                $articles = $response->json()['articles'] ?? [];

                if (!empty($articles)) {
                    return collect($articles)->map(function ($article) {
                        return [
                            'title' => $article['title'] ?? '-',
                            'sentiment' => $this->naiveSentiment($article['title'] ?? ''),
                        ];
                    })->toArray();
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Fallback: Default news jika API tidak tersedia
        $defaultNews = [
            'Germany' => [
                ['title' => 'Germany strengthens trade agreements with Southeast Asian partners', 'sentiment' => 'positive'],
                ['title' => 'European supply chain faces delays in Q2 2024', 'sentiment' => 'negative'],
                ['title' => 'German manufacturing sector remains stable amid global uncertainty', 'sentiment' => 'neutral'],
            ],
            'China' => [
                ['title' => 'China export growth accelerates beyond expectations', 'sentiment' => 'positive'],
                ['title' => 'Trade tensions create challenges for Chinese logistics', 'sentiment' => 'negative'],
                ['title' => 'Beijing announces new economic stimulus package', 'sentiment' => 'positive'],
            ],
            'Indonesia' => [
                ['title' => 'Indonesia boosts infrastructure investment for improved supply chain', 'sentiment' => 'positive'],
                ['title' => 'Port operations face seasonal disruptions', 'sentiment' => 'negative'],
                ['title' => 'Indonesian manufacturing sector shows growth potential', 'sentiment' => 'positive'],
            ],
            'Australia' => [
                ['title' => 'Australia strengthens trade links with Asian markets', 'sentiment' => 'positive'],
                ['title' => 'Shipping delays impact Australian exports', 'sentiment' => 'negative'],
                ['title' => 'Australian economy maintains steady recovery trajectory', 'sentiment' => 'neutral'],
            ],
            'Singapore' => [
                ['title' => 'Singapore port sees record container throughput', 'sentiment' => 'positive'],
                ['title' => 'Maritime congestion eases at Singapore strait', 'sentiment' => 'positive'],
                ['title' => 'Singapore tech investments boost supply chain', 'sentiment' => 'positive'],
            ],
            'United States' => [
                ['title' => 'US consumer demand drives logistics expansion', 'sentiment' => 'positive'],
                ['title' => 'West coast ports face minor labor delays', 'sentiment' => 'negative'],
                ['title' => 'New trade policies impact US supply chain', 'sentiment' => 'neutral'],
            ],
        ];

        return $defaultNews[$country] ?? [
            ['title' => 'Global supply chain updates and market trends', 'sentiment' => 'neutral'],
            ['title' => 'International trade continues with positive momentum', 'sentiment' => 'positive'],
            ['title' => 'Economic indicators show stable performance', 'sentiment' => 'neutral'],
        ];
    }

    protected function naiveSentiment(string $text): string
    {
        $positive = ['growth', 'increase', 'profit', 'stable', 'improve', 'rise', 'up', 'strong', 'boost', 'accelerate'];
        $negative = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'down', 'weak', 'challenge', 'disruption'];

        $text = strtolower($text);
        $posScore = 0;
        $negScore = 0;

        foreach ($positive as $word) {
            if (str_contains($text, $word)) $posScore++;
        }
        foreach ($negative as $word) {
            if (str_contains($text, $word)) $negScore++;
        }

        if ($posScore > $negScore) return 'positive';
        if ($negScore > $posScore) return 'negative';
        return 'neutral';
    }

    protected function formatBigNumber(float $value, bool $isCurrency = false): string
    {
        if ($value >= 1_000_000_000_000) {
            return round($value / 1_000_000_000_000, 1) . 'T';
        }
        if ($value >= 1_000_000_000) {
            return round($value / 1_000_000_000, 1) . 'B';
        }
        if ($value >= 1_000_000) {
            return round($value / 1_000_000, 1) . ($isCurrency ? 'M' : 'jt');
        }
        return number_format($value);
    }
}