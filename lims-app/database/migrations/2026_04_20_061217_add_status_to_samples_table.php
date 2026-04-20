<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->enum('status', [
                'registered',       // baru terdaftar
                'waiting_thermal',  // antrean pemanasan (US 2.1)
                'processing',       // sedang dipanaskan (US 2.3)
                'completed_thermal', // selesai dipanaskan (US 2.5)
                'ready_pcr',        // siap uji PCR
            ])->default('registered')->after('patient_name');
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
