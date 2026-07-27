<?php

namespace App\Http\Controllers;

class FavoriteMonitoringDashboardController extends Controller
{
    public function index()
    {
        $countries = \App\Models\Country::orderBy('name', 'asc')->get();
        return view('favorite_monitoring_dashboard', compact('countries'));
    }
}