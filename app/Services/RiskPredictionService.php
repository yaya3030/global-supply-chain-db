<?php

namespace App\Services;

class RiskPredictionService
{
    public function calculateGlobalRisk(float $weather, float $inflation, float $politicalSent, float $currency): float
    {
        // Normalisasi Political Sentiment (-1 sampai 1) ke skala Risiko (0 sampai 100)
        // Rumus: (1 - Sentiment) * 50
        $politicalRisk = (1 - $politicalSent) * 50;

        // Bobot: Weather 30%, Inflation 20%, Political 40%, Currency 10%
        $score = ($weather * 0.3) + ($inflation * 0.2) + ($politicalRisk * 0.4) + ($currency * 0.1);
        
        return round($score, 2);
    }
}