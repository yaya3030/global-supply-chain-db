<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\WeatherController; // Tambahkan ini

// Endpoints list
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/ports', [PortController::class, 'index']);
Route::get('/weather', [WeatherController::class, 'getWeather']); // Tambahkan ini