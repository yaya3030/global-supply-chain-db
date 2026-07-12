<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalCountryDashboardController;
use App\Http\Controllers\RiskScoringDashboardController;
use App\Http\Controllers\GlobalWeatherDashboardController;


Route::get('/', function () {
    return view('welcome');
});

// Fitur 1: Global Country Dashboard
Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index']);

// Fitur 2: Risk Scoring Engine
Route::get('/risk-scoring-dashboard', [RiskScoringDashboardController::class, 'index']);

// Fitur 3: Global Weather Monitoring (Terbaru)
Route::get('/global-weather-dashboard', [GlobalWeatherDashboardController::class, 'index']);