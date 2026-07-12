<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalCountryDashboardController;
use App\Http\Controllers\RiskScoringDashboardController;


Route::get('/', function () {
    return view('welcome');
});

// Fitur 1: Halaman Global Country Dashboard
Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index']);

// Fitur 2: Halaman Risk Scoring Engine (Terbaru)
Route::get('/risk-scoring-dashboard', [RiskScoringDashboardController::class, 'index']);