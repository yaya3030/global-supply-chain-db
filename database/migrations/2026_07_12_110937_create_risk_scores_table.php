<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('weather_score', 5, 2);
            $table->decimal('inflation_score', 5, 2);
            $table->decimal('exchange_rate_score', 5, 2);
            $table->decimal('news_sentiment_score', 5, 2);
            $table->decimal('final_risk_score', 5, 2);
            $table->date('calculated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};