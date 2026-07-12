<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Panggil service yang sudah ada
use App\Services\SentimentAnalysisService; 

class DashboardStatsController extends Controller
{
    public function getDashboardSummary()
    {
        // Di sini kamu bisa gabungkan data dari berbagai sumber
        // Simulasi data (Ganti ini dengan query ke DB atau API internal kamu)
        return response()->json([
            'total_countries' => 24, // Ganti dengan total dari DB
            'active_risks' => 5,     // Ganti dengan count dari tabel risk
            'latest_weather' => 'Sunny',
            'traffic_trend' => [10, 20, 15, 25, 30] // Data untuk grafik
        ]);
    }
}