<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GlobalCountryDashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index']);
