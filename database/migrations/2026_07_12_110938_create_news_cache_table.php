<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('source_name', 100);
            $table->text('url');
            $table->timestamp('published_at');
            $table->decimal('sentiment_score', 4, 2)->default(0.00); // Range -1.00 to 1.00
            $table->string('sentiment_label', 20)->default('neutral'); // positive, negative, neutral
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_cache');
    }
};