<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('port_name', 150);
            $table->string('port_code', 10)->nullable(); // Example: IDTPP
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};