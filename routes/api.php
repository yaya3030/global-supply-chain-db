<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\WorldBankController;
use App\Http\Controllers\Api\RESTCountriesController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\GNewsController;
use App\Http\Controllers\Api\WorldPortIndexController;
use App\Http\Controllers\GlobalCountryDashboardController;
use App\Http\Controllers\Api\RiskScoringController;
use App\Http\Controllers\Api\GlobalWeatherController;


// Modul Tahap 2 Integrasi API Eksternal
Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::get('/economic-indicators', [WorldBankController::class, 'getEconomicIndicators']);
Route::get('/country-details', [RESTCountriesController::class, 'getCountryDetails']);
Route::get('/exchange-rate', [ExchangeRateController::class, 'getExchangeRate']);
Route::get('/logistics-news', [GNewsController::class, 'getLogisticsNews']);
Route::get('/port-index', [WorldPortIndexController::class, 'getPortDetails']);

// Modul Tahap 3 Fitur Utama Aplikasi
Route::get('/countries-summary', [GlobalCountryDashboardController::class, 'getApiData']);
Route::get('/risk-scoring', [RiskScoringController::class, 'getRiskScores']);
Route::get('/global-weather-status', [GlobalWeatherController::class, 'getWeatherStatus']); // <-- Endpoint Baru