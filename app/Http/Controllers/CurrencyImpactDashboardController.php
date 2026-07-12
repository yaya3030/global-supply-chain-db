<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyImpactDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dampak mata uang.
     */
    public function index()
    {
        return view('currency_impact_dashboard');
    }
}