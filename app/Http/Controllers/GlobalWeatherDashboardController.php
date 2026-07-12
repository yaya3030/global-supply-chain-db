<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalWeatherDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard monitoring cuaca maritim.
     */
    public function index()
    {
        return view('global_weather_dashboard');
    }
}