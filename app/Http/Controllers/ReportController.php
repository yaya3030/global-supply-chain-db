<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\RiskScore;
use App\Exports\RiskScoreExport;

class ReportController extends Controller
{
    public function downloadPdf()
    {
        $riskScores = RiskScore::with('country')->orderBy('calculated_at', 'desc')->get();
        
        $pdf = Pdf::loadView('reports.risk_pdf', compact('riskScores'));
        
        return $pdf->download('risk_scores_report.pdf');
    }

    public function downloadExcel()
    {
        return Excel::download(new RiskScoreExport, 'risk_scores_report.xlsx');
    }
}
