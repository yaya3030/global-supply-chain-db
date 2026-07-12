<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('iso2', 2)->unique(); // Example: US, ID
            $table->string('iso3', 3)->unique(); // Example: USA, IDN
            $table->string('currency_code', 10); // Example: USD, IDR
            $table->string('region', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};