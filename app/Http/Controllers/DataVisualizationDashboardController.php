<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataVisualizationDashboardController extends Controller
{
    /**
     * Menampilkan halaman utama pusat visualisasi data kontrol.
     */
    public function index()
    {
        return view('data_visualization_dashboard');
    }
}