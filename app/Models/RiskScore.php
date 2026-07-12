<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id',
        'weather_score',
        'inflation_score',
        'exchange_rate_score',
        'news_sentiment_score',
        'final_risk_score',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'date',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}