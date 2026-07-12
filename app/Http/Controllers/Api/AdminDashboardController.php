<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;

class AdminDashboardController extends Controller
{
    protected $service;
    public function __construct(AdminDashboardService $service) { $this->service = $service; }

    public function getStats() {
        return response()->json(['status' => 'success', 'data' => $this->service->getSystemStats()]);
    }
}