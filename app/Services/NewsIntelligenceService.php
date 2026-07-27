<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class NewsIntelligenceService
{
    /** GNews API base URL */
    private string $gnewsBase = 'https://gnews.io/api/v4';

    /**
     * Category definitions with GNews-optimized search queries.
     * GNews supports: breaking-news, world, nation, business, technology,
     *                 entertainment, sports, science, health
     */
    private array $categories = [
        'logistics' => [
            'label'   => 'Logistics',
            'emoji'   => '🚛',
            'color'   => 'violet',
            'query'   => 'logistics OR "supply chain" OR freight OR warehouse',
        ],
        'trade' => [
            'label'   => 'Trade',
            'emoji'   => '🌐',
            'color'   => 'blue',
            'query'   => 'trade OR export OR import OR tariff OR "free trade agreement"',
        ],
        'shipping' => [
            'label'   => 'Shipping',
            'emoji'   => '🚢',
            'color'   => 'green',
            'query'   => 'shipping OR maritime OR "container ship" OR port OR cargo',
        ],
        'economy' => [
            'label'   => 'Economy',
            'emoji'   => '📈',
            'color'   => 'amber',
            'query'   => 'economy OR GDP OR inflation OR "interest rate" OR recession',
        ],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fetch news for a country + category from GNews API.
     * Falls back to rich generated articles if API key is missing or quota exceeded.
     */
    public function fetchByCountryAndCategory(string $country, string $category): array
    {
        if (!isset($this->categories[$category])) {
            $category = 'logistics';
        }

        $apiKey = config('services.gnews.key', '');

        // Cache key — 30 min for API results, 10 min for fallback
        $countryKey = strtolower(preg_replace('/\W+/', '_', $country));
        $cacheKey   = "gnews_{$countryKey}_{$category}_v5";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // ── 1. GNews API (primary) ────────────────────────────────────────────
        if (!empty($apiKey) && $apiKey !== 'your_gnews_api_key_here') {
            $articles = $this->callGNewsSearch($apiKey, $country, $category);
            if (!empty($articles)) {
                Cache::put($cacheKey, $articles, 1800); // 30 min
                return $articles;
            }
        }

        // ── 2. Fallback: generated articles with real clickable URLs ──────────
        $fallback = $this->buildFallbackArticles($country, $category);
        Cache::put($cacheKey, $fallback, 600); // 10 min
        return $fallback;
    }

    /**
     * Fetch global news (no country filter) per category.
     */
    public function fetchByCategory(string $category): array
    {
        return $this->fetchByCountryAndCategory('Global', $category);
    }

    /**
     * Return all categories metadata.
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Return country list from DB.
     */
    public function getCountryList(): array
    {
        try {
            $seen   = [];
            $result = [];
            foreach (Country::select('id', 'name', 'iso2')->orderBy('name')->get() as $c) {
                $name = trim($c->name);
                if (isset($seen[$name])) continue;
                $seen[$name] = true;
                $result[]    = ['id' => $c->id, 'name' => $name, 'iso2' => $c->iso2];
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Legacy analyzeLogisticsNews for /api/news-intelligence.
     */
    public function analyzeLogisticsNews(): array
    {
        $all = [];
        foreach (array_keys($this->categories) as $cat) {
            foreach ($this->fetchByCategory($cat) as $a) {
                $title = $a['title'] ?? '';
                if (preg_match('/(delay|strike|congestion|storm|blocked|risk|disruption|crisis|restrict|shortage)/i', $title)) {
                    $impact = 'Disruption'; $badgeColor = 'danger';
                } elseif (preg_match('/(growth|expand|efficient|improves|launch|record|surge|profit|boost|rise|invest)/i', $title)) {
                    $impact = 'Positive'; $badgeColor = 'success';
                } else {
                    $impact = 'Neutral'; $badgeColor = 'secondary';
                }
                $all[] = array_merge($a, [
                    'impact_category' => $impact,
                    'badge_color'     => $badgeColor,
                    'published_at'    => $a['publishedAt'] ?? now()->toDateTimeString(),
                ]);
            }
        }
        return $all;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: GNews API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build GNews search query and call the API.
     * Docs: https://gnews.io/docs/v4
     */
    private function callGNewsSearch(string $apiKey, string $country, string $category): array
    {
        $catQuery = $this->categories[$category]['query'];

        // Combine country name + category keywords
        if ($country !== 'Global') {
            $q = "\"{$country}\" AND ({$catQuery})";
        } else {
            $q = $catQuery;
        }

        $params = [
            'q'      => $q,
            'lang'   => 'en',
            'max'    => 9,         // up to 9 results (free plan allows 10 max)
            'sortby' => 'publishedAt',
            'apikey' => $apiKey,
        ];

        try {
            $url      = $this->gnewsBase . '/search?' . http_build_query($params);
            $response = Http::withoutVerifying()
                ->timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($url);

            if (!$response->successful()) {
                Log::warning("GNews API HTTP {$response->status()} for [{$country}][{$category}]");
                return [];
            }

            $data = $response->json();

            // GNews returns error in 'errors' key when quota exceeded
            if (!empty($data['errors'])) {
                Log::warning('GNews quota/error: ' . json_encode($data['errors']));
                return [];
            }

            if (empty($data['articles'])) {
                return [];
            }

            return array_map(
                fn($a) => $this->normalizeGnewsArticle($a, $category, $country, true),
                $data['articles']
            );

        } catch (Exception $e) {
            Log::error("GNews API exception [{$country}][{$category}]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Normalize a raw GNews article into our standard format.
     */
    private function normalizeGnewsArticle(array $a, string $category, string $country, bool $realtime = true): array
    {
        return [
            'title'       => $a['title'] ?? 'Untitled',
            'description' => $a['description'] ?? '',
            'content'     => $a['content'] ?? '',
            'url'         => $a['url'] ?? '#',
            'image'       => !empty($a['image']) ? $a['image'] : null,
            'publishedAt' => $a['publishedAt'] ?? now()->toIso8601String(),
            'source'      => [
                'name' => $a['source']['name'] ?? 'GNews',
                'url'  => $a['source']['url']  ?? '#',
            ],
            'category'    => $category,
            'country'     => $country,
            'realtime'    => $realtime,
            'via'         => 'gnews',
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: Fallback Articles (real clickable URLs)
    // ─────────────────────────────────────────────────────────────────────────

    private function buildFallbackArticles(string $country, string $category): array
    {
        $now = now();
        $c   = ($country === 'Global') ? 'Global Supply Chain' : $country;

        $realSources = [
            'logistics' => [
                ['name' => 'FreightWaves',         'url' => 'https://www.freightwaves.com/news'],
                ['name' => 'Supply Chain Brain',   'url' => 'https://www.supplychainbrain.com/articles/section/news'],
                ['name' => 'Logistics Management', 'url' => 'https://www.logisticsmgmt.com/'],
                ['name' => 'Reuters Business',     'url' => 'https://www.reuters.com/business/'],
                ['name' => 'The Loadstar',         'url' => 'https://theloadstar.com/'],
                ['name' => 'Global Trade Review',  'url' => 'https://www.gtreview.com/news/'],
            ],
            'trade' => [
                ['name' => 'Financial Times',      'url' => 'https://www.ft.com/trade'],
                ['name' => 'Reuters Business',     'url' => 'https://www.reuters.com/business/'],
                ['name' => 'Bloomberg Markets',    'url' => 'https://www.bloomberg.com/markets'],
                ['name' => 'Wall Street Journal',  'url' => 'https://www.wsj.com/news/business'],
                ['name' => 'WTO News',             'url' => 'https://www.wto.org/english/news_e/news_e.htm'],
                ['name' => 'Trade Finance Global', 'url' => 'https://www.tradefinanceglobal.com/news/'],
            ],
            'shipping' => [
                ['name' => 'Maritime Executive',   'url' => 'https://www.maritimeexecutive.com/'],
                ['name' => 'Hellenic Shipping',    'url' => 'https://www.hellenicshippingnews.com/'],
                ['name' => 'Seatrade Maritime',    'url' => 'https://www.seatrade-maritime.com/'],
                ['name' => 'TradeWinds',           'url' => 'https://www.tradewindsnews.com/'],
                ['name' => "Lloyd's List",         'url' => 'https://lloydslist.maritimeintelligence.informa.com/'],
                ['name' => 'Splash247',            'url' => 'https://splash247.com/'],
            ],
            'economy' => [
                ['name' => 'IMF News',             'url' => 'https://www.imf.org/en/News'],
                ['name' => 'World Bank News',      'url' => 'https://www.worldbank.org/en/news'],
                ['name' => 'Reuters Finance',      'url' => 'https://www.reuters.com/business/finance/'],
                ['name' => 'Bloomberg Economics',  'url' => 'https://www.bloomberg.com/economics'],
                ['name' => 'Financial Times',      'url' => 'https://www.ft.com/global-economy'],
                ['name' => 'The Economist',        'url' => 'https://www.economist.com/economy'],
            ],
        ];

        $templates = [
            'logistics' => [
                ["{$c} Accelerates Digital Transformation Across Logistics Networks", "{$c} is deploying AI-powered route optimization and real-time cargo tracking to reduce freight transit times by up to 25% across key corridors.", 0, 2],
                ["{$c} Port Expansion Project Approved to Handle 40% Growth in Cargo Volume", "Authorities greenlit a multi-billion investment in port infrastructure to meet surging demand from global trade partners and e-commerce platforms.", 1, 5],
                ["{$c} Adopts Cold Chain Standards to Meet Global Food Export Requirements", "Modernizing temperature-controlled transport and cold chain infrastructure across major distribution hubs to comply with international food safety regulations.", 2, 12],
                ["{$c} Launches Last-Mile Delivery Pilot with Electric Vehicles in Urban Zones", "A government-backed EV cargo program targets 30% reduction in inner-city logistics emissions while cutting last-mile delivery costs.", 3, 24],
                ["{$c} Faces Freight Bottlenecks Amid Truck Driver Shortage Crisis", "Industry associations warn critical driver shortages are creating bottlenecks in domestic freight movement and threatening on-time delivery rates.", 4, 36],
                ["{$c} Signs Cross-Border Logistics Cooperation Agreement to Cut Clearance Times", "New bilateral logistics framework to streamline cross-border cargo procedures, reducing customs clearance times by an estimated 40%.", 5, 48],
            ],
            'trade' => [
                ["{$c} Records Strong Export Growth Driven by Manufacturing and Commodities", "Trade data shows a 14% year-on-year rise in exports, led by manufactured goods and key commodity sectors including energy and agriculture.", 0, 1],
                ["{$c} Negotiates New Free Trade Agreement with Major Economic Bloc", "High-level negotiations in final stages, with both parties expecting to reduce tariffs on thousands of product categories.", 1, 6],
                ["{$c} Tightens Import Regulations on Electronics to Protect Domestic Industry", "New compliance requirements and import duties on consumer electronics announced as part of broader industrial policy to support local manufacturers.", 2, 18],
                ["{$c} Trade Deficit Narrows as Exports Surge to Three-Year High", "Balance of trade figures show significant narrowing, as export volumes across key sectors reached their highest level in three years.", 3, 30],
                ["{$c} Joins Regional Trade Framework, Unlocking 500M-Consumer Market Access", "Following ratification of a regional trade agreement, businesses now have preferential access to a combined consumer market of over 500 million people.", 4, 48],
                ["{$c} Export Sector Warns of Currency Volatility Impact on Competitiveness", "Exporters calling on the central bank to intervene as recent currency fluctuations have eroded profit margins in global markets.", 5, 60],
            ],
            'shipping' => [
                ["{$c} Major Port Reports Record Container Throughput in First Half of Year", "Primary container port logged a record 8.2 million TEU in H1, cementing its role as a critical node in the global shipping network.", 0, 3],
                ["{$c} Invests \$2B in Green Port Infrastructure and Zero-Emission Vessel Facilities", "Government commits funding for LNG bunkering stations, shore power infrastructure, and green hydrogen refueling points across major seaports.", 1, 8],
                ["{$c} Shipping Lines Expand Fleet with 15 New Ultra-Large Container Vessels", "National carriers place orders for mega container ships exceeding 24,000 TEU each to meet growing transoceanic cargo demand.", 2, 20],
                ["{$c} Port Workers Strike Threat Raises Fears of Regional Cargo Disruption", "Trade union strike warning follows wage negotiation breakdown, with analysts warning of potential disruption to regional shipping routes.", 3, 32],
                ["{$c} Opens New Deep-Water Terminal to Accommodate Next-Generation Mega Ships", "New deep-draft terminal capable of berthing vessels with up to 22-meter draught is now open to commercial traffic.", 4, 50],
                ["{$c} Adopts IMO Carbon Intensity Standards Ahead of 2027 Deadline", "Maritime authority mandates early CII compliance for all registered vessels, positioning the country as a leader in sustainable shipping.", 5, 72],
            ],
            'economy' => [
                ["{$c} GDP Grows 4.8% in Latest Quarter, Beating Market Expectations", "Economy expanded faster than expected, driven by consumer spending, export growth, and robust foreign direct investment inflows.", 0, 4],
                ["{$c} Central Bank Holds Interest Rates Amid Signs of Cooling Inflation", "Benchmark rate unchanged as inflation trends improve, but global uncertainty flagged as a key downside risk to the outlook.", 1, 7],
                ["{$c} Inflation Drops to 3.2% as Food and Energy Prices Stabilize", "Consumer price inflation eased to its lowest in 18 months, giving the central bank room to consider rate cuts in H2.", 2, 24],
                ["{$c} Attracts Record \$18B in Foreign Direct Investment in First Half", "Record FDI confirmed with major inflows into manufacturing, technology, and clean energy sectors.", 3, 36],
                ["{$c} Unemployment Falls to 15-Year Low as Labor Market Tightens", "Unemployment at decade low, reflecting strong hiring in services, technology, and manufacturing despite global headwinds.", 4, 48],
                ["{$c} Issues \$5B Sovereign Green Bond to Finance Climate Infrastructure Projects", "Government successfully placed a green bond with proceeds earmarked for renewable energy, electric transport, and coastal resilience.", 5, 72],
            ],
        ];

        $images = [
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=600',
            'https://images.unsplash.com/photo-1578575437130-527eed3abbec?q=80&w=600',
            'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?q=80&w=600',
            'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=600',
            'https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?q=80&w=600',
            'https://images.unsplash.com/photo-1527977966376-1c8408f9f108?q=80&w=600',
        ];

        $catTemplates = $templates[$category] ?? $templates['logistics'];
        $catSources   = $realSources[$category] ?? $realSources['logistics'];

        return array_map(function ($t) use ($now, $catSources, $images, $category, $country) {
            $idx    = $t[2];
            $source = $catSources[$idx] ?? $catSources[0];
            return [
                'title'       => $t[0],
                'description' => $t[1],
                'url'         => $source['url'],
                'image'       => $images[$idx] ?? $images[0],
                'publishedAt' => $now->copy()->subHours($t[3])->toIso8601String(),
                'source'      => ['name' => $source['name'], 'url' => $source['url']],
                'category'    => $category,
                'country'     => $country,
                'realtime'    => false,
                'via'         => 'fallback',
            ];
        }, $catTemplates);
    }
}