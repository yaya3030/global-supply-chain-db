<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Services\WorldPortIndexService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorldPortIndexController extends Controller
{
    protected $wpiService;

    // Dependency Injection
    public function __construct(WorldPortIndexService $wpiService)
    {
        $this->wpiService = $wpiService;
    }

    public function getPortDetails(Request $request): JsonResponse
    {
        // 1. Validasi parameter input
        $request->validate([
            'port_id' => 'required|exists:ports,id',
        ]);

        // 2. Ambil data pelabuhan dari database
        $port = Port::find($request->input('port_id'));

        // 3. Panggil Service menggunakan kode dan nama pelabuhan
        $portCode = $port->port_code ?? 'N/A';
        $wpiData = $this->wpiService->getPortAttributes($portCode, $port->port_name);

        // 4. Kirim respons JSON bersih
        return response()->json([
            'status' => 'success',
            'port_id' => $port->id,
            'database_name' => $port->port_name,
            'world_port_index' => [
                'official_name' => $wpiData['PORT_NAME'] ?? $port->port_name,
                'index_number' => $wpiData['INDEX_NO'] ?? 'N/A',
                'harbor_size' => $wpiData['HARBORSIZE'] ?? 'Unknown',
                'shelter_afforded' => $wpiData['SHELTER'] ?? 'Unknown',
                'max_vessel_draft' => $wpiData['MAX_DRAFT'] ?? 'N/A',
                'tug_availability' => $wpiData['TUG_ASSIST'] ?? 'N/A',
            ],
            'fetched_at' => now()->toDateTimeString()
        ], 200);
    }
}