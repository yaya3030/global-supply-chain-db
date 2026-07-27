<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_id')->constrained('goods')->onDelete('cascade');
            $table->foreignId('port_id')->constrained('ports')->onDelete('cascade');
            $table->enum('status', ['arrived', 'departed', 'delayed']);
            $table->timestamp('arrival_time')->nullable();
            $table->timestamp('departure_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_history');
    }
};
