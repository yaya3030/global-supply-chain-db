<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected array $defaultCountries = ['Germany', 'China', 'Indonesia', 'Australia'];

    protected array $countryCodes = [
        'Germany'   => ['iso2' => 'DE', 'iso3' => 'DEU', 'currency' => 'EUR', 'lat' => 51.1657, 'lng' => 10.4515],
        'China'     => ['iso2' => 'CN', 'iso3' => 'CHN', 'currency' => 'CNY', 'lat' => 35.8617, 'lng' => 104.1954],
        'Indonesia' => ['iso2' => 'ID', 'iso3' => 'IDN', 'currency' => 'IDR', 'lat' => -0.7893, 'lng' => 113.9213],
        'Australia' => ['iso2' => 'AU', 'iso3' => 'AUS', 'currency' => 'AUD', 'lat' => -25.2744, 'lng' => 133.7751],
    ];

    public function index()
    {
        return view('dashboard', [
            'countries' => $this->defaultCountries,
        ]);
    }

    public function countryData(Request $request)
    {
        $country = $request->query('country', 'Germany');
        $meta = $this->countryCodes[$country] ?? null;

        if (!$meta) {
            return response()->json(['message' => 'Negara tidak dikenal'], 404);
        }

        $cacheKey = 'dashboard_country_' . $meta['iso2'];

        $result = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($country, $meta) {
            return [
                'population' => $this->getPopulation($meta['iso3']),
                'gdp'        => $this->getGdp($meta['iso3']),
                'inflation'  => $this->getInflation($meta['iso3']),
                'currency'   => $this->getCurrency($meta['currency']),
                'weather'    => $this->getWeather($meta['lat'], $meta['lng']),
                'risk'       => $this->getRiskScore($country),
                'trend'      => $this->getRiskTrend($country),
                'news'       => $this->getNews($country),
            ];
        });

        return response()->json($result);
    }

    protected function getPopulation(string $iso3): string
    {
        try {
            $response = Http::timeout(5)->get(
                "https://api.worldbank.org/v2/country/{$iso3}/indicator/SP.POP.TOTL",
                ['format' => 'json', 'per_page' => 1, 'mrnev' => 1]
            );

            $value = $response->json()[1][0]['value'] ?? null;

            if (!$value) return '-';

            return $this->formatBigNumber($value);
        } catch (\Throwable $e) {
            report($e);
            return '-';
        }
    }

    protected function getGdp(string $iso3): string
    {
        try {
            $response = Http::timeout(5)->get(
                "https://api.worldbank.org/v2/country/{$iso3}/indicator/NY.GDP.MKTP.CD",
                ['format' => 'json', 'per_page' => 1, 'mrnev' => 1]
            );

            $value = $response->json()[1][0]['value'] ?? null;

            if (!$value) return '-';

            return '$' . $this->formatBigNumber($value, true);
        } catch (\Throwable $e) {
            report($e);
            return '-';
        }
    }

    protected function getInflation(string $iso3): string
    {
        try {
            $response = Http::timeout(5)->get(
                "https://api.worldbank.org/v2/country/{$iso3}/indicator/FP.CPI.TOTL.ZG",
                ['format' => 'json', 'per_page' => 1, 'mrnev' => 1]
            );

            $value = $response->json()[1][0]['value'] ?? null;

            if ($value === null) return '-';

            return round($value, 1) . '%';
        } catch (\Throwable $e) {
            report($e);
            return '-';
        }
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
                return ['rate_change_percent' => '0.0', 'direction' => 'up'];
            }

            $yesterdayKey = 'fx_yesterday_' . $currencyCode;
            $yesterdayRate = Cache::get($yesterdayKey, $currentRate);

            Cache::put($yesterdayKey, $currentRate, now()->addDay());

            $changePercent = $yesterdayRate != 0
                ? (($currentRate - $yesterdayRate) / $yesterdayRate) * 100
                : 0;

            return [
                'rate_change_percent' => number_format(abs($changePercent), 2),
                'direction' => $changePercent >= 0 ? 'up' : 'down',
            ];
        } catch (\Throwable $e) {
            report($e);
            return ['rate_change_percent' => '0.0', 'direction' => 'up'];
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

        if (!$apiKey) {
            return [];
        }

        try {
            $response = Http::timeout(5)->get('https://gnews.io/api/v4/search', [
                'q' => $country . ' trade OR shipping OR economy',
                'lang' => 'en',
                'max' => 3,
                'apikey' => $apiKey,
            ]);

            $articles = $response->json()['articles'] ?? [];

            return collect($articles)->map(function ($article) {
                return [
                    'title' => $article['title'] ?? '-',
                    'sentiment' => $this->naiveSentiment($article['title'] ?? ''),
                ];
            })->toArray();
        } catch (\Throwable $e) {
            report($e);
            return [];
        }
    }

    protected function naiveSentiment(string $text): string
    {
        $positive = ['growth', 'increase', 'profit', 'stable', 'improve', 'rise', 'up'];
        $negative = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'down'];

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
        if ($negScore > $posScore) return 'neutral';
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