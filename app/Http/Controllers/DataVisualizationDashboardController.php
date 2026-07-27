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
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        return view('data_visualization_dashboard', compact('countries'));
    }
}