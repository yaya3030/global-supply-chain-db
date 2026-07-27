<?php

namespace App\Exports;

use App\Models\RiskScore;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiskScoreExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return RiskScore::with('country')->orderBy('calculated_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Country Code',
            'Country Name',
            'Weather Score',
            'Inflation Score',
            'Exchange Rate Score',
            'News Sentiment Score',
            'Final Risk Score',
            'Calculated At',
        ];
    }

    public function map($riskScore): array
    {
        return [
            $riskScore->country->iso3 ?? 'N/A',
            $riskScore->country->name ?? 'N/A',
            $riskScore->weather_score,
            $riskScore->inflation_score,
            $riskScore->exchange_rate_score,
            $riskScore->news_sentiment_score,
            $riskScore->final_risk_score,
            $riskScore->calculated_at,
        ];
    }
}
