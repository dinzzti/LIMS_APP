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
        Schema::create('thermal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained('samples')->onDelete('cascade');
            $table->decimal('temperature_celsius', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thermal_logs');
    }
};