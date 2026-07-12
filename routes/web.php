<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalCountryDashboardController;
use App\Http\Controllers\RiskScoringDashboardController;
use App\Http\Controllers\GlobalWeatherDashboardController;
use App\Http\Controllers\CurrencyImpactDashboardController;


Route::get('/', function () {
    return view('welcome');
});

// Fitur 1: Global Country Dashboard
Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index']);

// Fitur 2: Risk Scoring Engine
Route::get('/risk-scoring-dashboard', [RiskScoringDashboardController::class, 'index']);

// Fitur 3: Global Weather Monitoring
Route::get('/global-weather-dashboard', [GlobalWeatherDashboardController::class, 'index']);

// Fitur 4: Currency Impact Dashboard (Terbaru)
Route::get('/currency-impact-dashboard', [CurrencyImpactDashboardController::class, 'index']);