<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PortLocationDashboardController extends Controller
{
    /**
     * Menampilkan halaman peta interaktif sebaran pelabuhan.
     */
    public function index()
    {
        return view('port_location_dashboard');
    }
}