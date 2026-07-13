<?php

use Illuminate\Support\Facades\Route;

// Import Controller API
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\WorldBankController;
use App\Http\Controllers\Api\RESTCountriesController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\GNewsController;
use App\Http\Controllers\Api\WorldPortIndexController;
use App\Http\Controllers\Api\RiskScoringController;
use App\Http\Controllers\Api\GlobalWeatherController;
use App\Http\Controllers\Api\CurrencyImpactController;
use App\Http\Controllers\Api\NewsIntelligenceController;
use App\Http\Controllers\Api\PortLocationController;
use App\Http\Controllers\Api\DataVisualizationController;
use App\Http\Controllers\Api\CountryComparisonController;
use App\Http\Controllers\Api\FavoriteMonitoringController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\RiskAnalysisController;
use App\Http\Controllers\Api\RealtimeCurrencyController;
use App\Http\Controllers\Api\RealtimePortController;
use App\Http\Controllers\DashboardController;

// Import Controller Web (Khusus untuk beberapa API yang dipanggil dari Dashboard)
use App\Http\Controllers\GlobalCountryDashboardController;


// Modul Tahap 2: Integrasi API Eksternal
Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::get('/economic-indicators', [WorldBankController::class, 'getEconomicIndicators']);
Route::get('/country-details', [RESTCountriesController::class, 'getCountryDetails']);
Route::get('/exchange-rate', [ExchangeRateController::class, 'getExchangeRate']);
Route::get('/logistics-news', [GNewsController::class, 'getLogisticsNews']);
Route::get('/port-index', [WorldPortIndexController::class, 'getPortDetails']);

// Modul Tahap 3: Fitur Utama Aplikasi
Route::get('/countries-summary', [GlobalCountryDashboardController::class, 'getApiData']);
Route::get('/risk-scoring', [RiskScoringController::class, 'getRiskScores']);
Route::get('/global-weather-status', [GlobalWeatherController::class, 'getWeatherStatus']);
Route::get('/currency-impact-analysis', [CurrencyImpactController::class, 'getImpactAnalysis']);
Route::get('/news-intelligence', [NewsIntelligenceController::class, 'getNewsAnalytics']);
Route::get('/port-locations', [PortLocationController::class, 'getLocations']);
Route::get('/data-visualization-metrics', [DataVisualizationController::class, 'getMetrics']);
Route::get('/country-comparison-data', [CountryComparisonController::class, 'getComparisonMetrics']);
Route::get('/favorite-monitoring', [FavoriteMonitoringController::class, 'getFavorites']);
Route::get('/admin-stats', [AdminDashboardController::class, 'getStats']);
Route::get('/dashboard-summary', [App\Http\Controllers\DashboardStatsController::class, 'getDashboardSummary']);
Route::get('/dashboard/country-data', [DashboardController::class, 'countryData']);


// ===== MODUL TAMBAHAN: REALTIME CURRENCY (FITUR BARU) =====
Route::prefix('currency')->group(function () {
    Route::get('/realtime', [RealtimeCurrencyController::class, 'getRealtime']);
    Route::get('/trend', [RealtimeCurrencyController::class, 'getTrend']);
    Route::get('/comparison', [RealtimeCurrencyController::class, 'getComparison']);
    Route::post('/refresh', [RealtimeCurrencyController::class, 'refreshCache']);
});

// ===== MODUL TAMBAHAN: REALTIME PORT DATA (FITUR BARU) =====
Route::prefix('port')->group(function () {
    Route::get('/realtime', [RealtimePortController::class, 'getRealtime']);
    Route::get('/comparison', [RealtimePortController::class, 'getComparison']);
    Route::post('/refresh', [RealtimePortController::class, 'refreshCache']);
});


// Modul Tahap 4: Risk Analysis (Wajib gunakan POST)
Route::post('/analyze-risk', [RiskAnalysisController::class, 'analyze']);