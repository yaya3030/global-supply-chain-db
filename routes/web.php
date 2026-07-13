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
use App\Http\Controllers\AdminDashboardController;

// Halaman Utama
Route::get('/', function () { return view('welcome'); });

// Fitur Dashboard Utama (DashboardController)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Fitur Lainnya (Ditambahkan ->name agar sidebar bisa dipencet)
Route::get('/risk-scoring-dashboard', [RiskScoringDashboardController::class, 'index'])->name('risk.index');
Route::get('/global-weather-dashboard', [GlobalWeatherDashboardController::class, 'index'])->name('weather.index');
Route::get('/currency-impact-dashboard', [CurrencyImpactDashboardController::class, 'index'])->name('currency.index');
Route::get('/news-intelligence-dashboard', [NewsIntelligenceDashboardController::class, 'index'])->name('news.index');
Route::get('/port-location-dashboard', [PortLocationDashboardController::class, 'index'])->name('ports.index');
Route::get('/country-comparison-dashboard', [CountryComparisonDashboardController::class, 'index'])->name('comparison.index');

// Fitur Lainnya
Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index'])->name('global.country');
Route::get('/data-visualization-dashboard', [DataVisualizationDashboardController::class, 'index'])->name('visualization.index');

// Fitur Favorite & Admin
Route::get('/favorite-monitoring-dashboard', [FavoriteMonitoringDashboardController::class, 'index'])->name('favorites.index');
Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.index');