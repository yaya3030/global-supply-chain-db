<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [
        'country_id',
        'title',
        'description',
        'source_name',
        'url',
        'published_at',
        'sentiment_score',
        'sentiment_label',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}