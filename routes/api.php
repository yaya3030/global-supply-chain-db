<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\WorldBankController;
use App\Http\Controllers\Api\RESTCountriesController;
use App\Http\Controllers\Api\ExchangeRateController; 

// Hanya aktifkan rute yang Controller-nya sudah kita buat dan pasti ada filenya
Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::get('/economic-indicators', [WorldBankController::class, 'getEconomicIndicators']);
Route::get('/country-details', [RESTCountriesController::class, 'getCountryDetails']);
Route::get('/exchange-rate', [ExchangeRateController::class, 'getExchangeRate']);
