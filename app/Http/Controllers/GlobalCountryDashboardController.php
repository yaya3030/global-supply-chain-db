<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class GlobalCountryDashboardController extends Controller
{
    /**
     * Menampilkan halaman utama Blade Dashboard.
     */
    public function index()
    {
        return view('global_country_dashboard');
    }

    /**
     * Endpoint REST API untuk menyuplai data ke AJAX dan Chart.js.
     */
    public function getApiData(): JsonResponse
    {
        try {
            // Query Database: Mengambil data negara sekaligus menghitung relasi jumlah pelabuhan
            $countries = Country::withCount('ports')->get();
        } catch (Exception $e) {
            // Fallback Engine: Jika relasi belum didefinisikan di Model, kode tetap berjalan normal
            $countries = Country::all()->map(function ($country) {
                $country->ports_count = $country->ports_count ?? 0;
                return $country;
            });
        }

        return response()->json([
            'status' => 'success',
            'summary' => [
                'total_countries' => $countries->count(),
                'total_monitored_ports' => $countries->sum('ports_count'),
            ],
            'data' => $countries
        ], 200);
    }
}