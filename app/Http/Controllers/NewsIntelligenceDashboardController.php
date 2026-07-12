<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsIntelligenceDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard intelijen berita logistik.
     */
    public function index()
    {
        return view('news_intelligence_dashboard');
    }
}