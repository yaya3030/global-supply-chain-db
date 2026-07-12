<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LexiconWordsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $positiveWords = [
            'growth', 'recovery', 'expansion', 'stable', 'increase', 'surge', 'boom', 
            'profit', 'surplus', 'boost', 'gain', 'upgrade', 'improvement', 'progress', 
            'robust', 'thrive', 'advance', 'optimization', 'strategic', 'security', 
            'alliance', 'deal', 'agreement', 'success', 'bullish', 'steady', 'efficient', 
            'rise', 'peak', 'wealth', 'benefit', 'strengthen', 'innovation', 'accelerate'
        ];

        $negativeWords = [
            'recession', 'inflation', 'drop', 'crisis', 'decline', 'shortage', 'risk', 
            'danger', 'delay', 'bottleneck', 'strike', 'conflict', 'war', 'sanction', 
            'collapse', 'deficit', 'tariff', 'congestion', 'protest', 'hazard', 'tension', 
            'crash', 'bankruptcy', 'loss', 'damage', 'failure', 'shutdown', 'threat', 
            'embargo', 'lockdown', 'bearish', 'volatile', 'disruption', 'blockade'
        ];

        // Insert Positive Words
        $posData = array_map(function($word) use ($now) {
            return ['word' => $word, 'created_at' => $now, 'updated_at' => $now];
        }, $positiveWords);
        DB::table('positive_words')->insertOrIgnore($posData);

        // Insert Negative Words
        $negData = array_map(function($word) use ($now) {
            return ['word' => $word, 'created_at' => $now, 'updated_at' => $now];
        }, $negativeWords);
        DB::table('negative_words')->insertOrIgnore($negData);
    }
}