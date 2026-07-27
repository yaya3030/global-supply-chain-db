<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CountryComparisonDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard komparasi negara.
     */
    public function index()
    {
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        return view('country_comparison_dashboard', compact('countries'));
    }
}