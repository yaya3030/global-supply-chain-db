<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user'); // admin or user
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $blueprint) {
            $blueprint->string('email')->primary();
            $blueprint->string('token');
            $blueprint->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $blueprint) {
            $blueprint->string('id')->primary();
            $blueprint->foreignId('user_id')->nullable()->index();
            $blueprint->string('ip_address', 45)->nullable();
            $blueprint->text('user_agent')->nullable();
            $blueprint->longText('payload');
            $blueprint->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};