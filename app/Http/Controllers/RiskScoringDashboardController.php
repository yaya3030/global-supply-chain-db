<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskScoringDashboardController extends Controller
{
    /**
     * Menampilkan halaman utama visualisasi Risk Scoring Engine.
     */
    public function index()
    {
        return view('risk_scoring_dashboard');
    }
}