<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentDictionary extends Model
{
    protected $fillable = ['word', 'type'];
    public $timestamps = false; // Jika tabel tidak punya created_at/updated_at
}