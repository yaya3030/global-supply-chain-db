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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

// Halaman Utama
Route::get('/', function () { return view('welcome'); })->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Fitur Terproteksi (Hanya User Login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/risk-scoring-dashboard', [RiskScoringDashboardController::class, 'index'])->name('risk.index');
    Route::get('/global-weather-dashboard', [GlobalWeatherDashboardController::class, 'index'])->name('weather.index');
    Route::get('/currency-impact-dashboard', [CurrencyImpactDashboardController::class, 'index'])->name('currency.index');
    Route::get('/news-intelligence-dashboard', [NewsIntelligenceDashboardController::class, 'index'])->name('news.index');
    Route::get('/port-location-dashboard', [PortLocationDashboardController::class, 'index'])->name('ports.index');
    Route::get('/country-comparison-dashboard', [CountryComparisonDashboardController::class, 'index'])->name('comparison.index');
    Route::get('/global-country-dashboard', [GlobalCountryDashboardController::class, 'index'])->name('global.country');
    Route::get('/data-visualization-dashboard', [DataVisualizationDashboardController::class, 'index'])->name('visualization.index');
    Route::get('/favorite-monitoring-dashboard', [FavoriteMonitoringDashboardController::class, 'index'])->name('favorites.index');

    // Report Downloads
    Route::get('/report/pdf', [ReportController::class, 'downloadPdf'])->name('report.pdf');
    Route::get('/report/excel', [ReportController::class, 'downloadExcel'])->name('report.excel');

    // Khusus Admin
    Route::middleware('is_admin')->group(function () {
        Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.index');
        
        // Admin CRUD API Routes
        Route::prefix('admin-api')->group(function () {
            Route::get('/overview', [AdminDashboardController::class, 'getOverview']);
            Route::get('/countries', [AdminDashboardController::class, 'getCountries']);
            
            // Users
            Route::get('/users', [AdminDashboardController::class, 'getUsers']);
            Route::post('/users', [AdminDashboardController::class, 'storeUser']);
            Route::put('/users/{id}', [AdminDashboardController::class, 'updateUser']);
            Route::delete('/users/{id}', [AdminDashboardController::class, 'destroyUser']);
            
            // Ports
            Route::get('/ports', [AdminDashboardController::class, 'getPorts']);
            Route::post('/ports', [AdminDashboardController::class, 'storePort']);
            Route::put('/ports/{id}', [AdminDashboardController::class, 'updatePort']);
            Route::delete('/ports/{id}', [AdminDashboardController::class, 'destroyPort']);
            
            // Articles
            Route::get('/articles', [AdminDashboardController::class, 'getArticles']);
            Route::post('/articles', [AdminDashboardController::class, 'storeArticle']);
            Route::put('/articles/{id}', [AdminDashboardController::class, 'updateArticle']);
            Route::delete('/articles/{id}', [AdminDashboardController::class, 'destroyArticle']);
        });
    });
});