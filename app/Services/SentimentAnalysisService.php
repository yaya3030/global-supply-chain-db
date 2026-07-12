<?php

namespace App\Services;

use App\Models\SentimentDictionary;

class SentimentAnalysisService
{
    public function analyze(string $text): float
    {
        // 1. Bersihkan teks
        $cleanText = strtolower(preg_replace('/[^\w\s]/', '', $text));
        $words = explode(' ', $cleanText);
        $words = array_filter($words); // Hilangkan elemen kosong
        
        // 2. Ambil kamus dari database
        $posWords = SentimentDictionary::where('type', 'positive')->pluck('word')->toArray();
        $negWords = SentimentDictionary::where('type', 'negative')->pluck('word')->toArray();

        $posCount = 0;
        $negCount = 0;
        $totalWords = count($words);

        if ($totalWords === 0) return 0.0;

        foreach ($words as $word) {
            if (in_array($word, $posWords)) $posCount++;
            if (in_array($word, $negWords)) $negCount++;
        }

        // Skor -1 sampai 1
        return round(($posCount - $negCount) / $totalWords, 2);
    }
}