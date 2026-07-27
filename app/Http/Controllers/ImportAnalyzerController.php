<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Port;
use App\Services\ImportAnalyzerService;

class ImportAnalyzerController extends Controller
{
    protected $analyzerService;

    public function __construct(ImportAnalyzerService $analyzerService)
    {
        $this->analyzerService = $analyzerService;
    }

    public function index()
    {
        $countries = Country::orderBy('name', 'asc')->get();
        return view('import_analyzer_dashboard', compact('countries'));
    }

    public function getPortsByCountry($country_id)
    {
        $ports = Port::where('country_id', $country_id)->orderBy('port_name', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $ports
        ]);
    }

    public function analyzeImport(Request $request)
    {
        $request->validate([
            'origin_country_id' => 'required|exists:countries,id',
            'origin_port_id' => 'required|exists:ports,id',
            'dest_country_id' => 'required|exists:countries,id',
            'dest_port_id' => 'required|exists:ports,id',
        ]);

        $analysis = $this->analyzerService->calculateRisk(
            $request->origin_country_id,
            $request->origin_port_id,
            $request->dest_country_id,
            $request->dest_port_id
        );

        return response()->json([
            'status' => 'success',
            'data' => $analysis
        ]);
    }
}
