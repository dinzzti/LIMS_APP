<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Di file SAMPLES, kodenya harus Schema::create('samples'...)
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('sample_code')->unique();
            $table->string('patient_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};