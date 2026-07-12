<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalCountryDashboardController;
use App\Http\Controllers\RiskScoringDashboardController;
use App\Http\Controllers\GlobalWeatherDashboardController;
use App\Http\Controllers\CurrencyImpactDashboardController;
use App\Http\Controllers\NewsIntelligenceDashboardController;
use App\Http\Controllers\PortLocationDashboardController;
use App\Http\Controllers\DataVisualizationDashboardController;
use App\Http\Controllers\CountryComparisonDashboardController;
use App\Http\Controllers\FavoriteMonitoringDashboardController;
use App\Http\Controllers\DashboardController;



Route::get('/', function () { return view('welcome'); });

// Fitur Dashboard Utama
Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index']);
Route::get('/risk-scoring-dashboard', [RiskScoringDashboardController::class, 'index']);
Route::get('/global-weather-dashboard', [GlobalWeatherDashboardController::class, 'index']);
Route::get('/currency-impact-dashboard', [CurrencyImpactDashboardController::class, 'index']);
Route::get('/news-intelligence-dashboard', [NewsIntelligenceDashboardController::class, 'index']);
Route::get('/port-location-dashboard', [PortLocationDashboardController::class, 'index']);
Route::get('/data-visualization-dashboard', [DataVisualizationDashboardController::class, 'index']);
Route::get('/country-comparison-dashboard', [CountryComparisonDashboardController::class, 'index']);

// Fitur Baru: Favorite Monitoring
Route::get('/favorite-monitoring-dashboard', [FavoriteMonitoringDashboardController::class, 'index']);
Route::get('/admin-dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index']);
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
