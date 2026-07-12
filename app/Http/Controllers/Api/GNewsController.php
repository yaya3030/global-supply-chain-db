<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\GNewsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GNewsController extends Controller
{
    protected $gnewsService;

    // Dependency Injection
    public function __construct(GNewsService $gnewsService)
    {
        $this->gnewsService = $gnewsService;
    }

    public function getLogisticsNews(Request $request): JsonResponse
    {
        // 1. Validasi parameter input
        $request->validate([
            'country_id' => 'required|exists:countries,id',
        ]);

        // 2. Cari data negara berdasarkan ID
        $country = Country::find($request->input('country_id'));

        // 3. Panggil Service untuk mengambil berita logistik terhangat
        $articles = $this->gnewsService->getCountryNews($country->name);

        // 4. Kirim respons JSON sukses yang bersih dan terstruktur
        return response()->json([
            'status' => 'success',
            'country' => $country->name,
            'topic' => 'Logistics & Supply Chain News',
            'total_results' => count($articles),
            'articles' => $articles,
            'cached_at' => now()->toDateTimeString()
        ], 200);
    }
}