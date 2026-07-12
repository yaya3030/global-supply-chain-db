<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'currency_code',
        'region',
    ];

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function newsCaches(): HasMany
    {
        return $this->hasMany(NewsCache::class);
    }

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}