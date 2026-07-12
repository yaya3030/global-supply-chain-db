<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\WorldBankController;

// Hanya aktifkan rute yang Controller-nya sudah kita buat dan pasti ada filenya
Route::get('/weather', [WeatherController::class, 'getWeather']);
Route::get('/economic-indicators', [WorldBankController::class, 'getEconomicIndicators']);