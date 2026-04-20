<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thermal_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_minutes')
                ->after('temperature_celsius')
                ->comment('Durasi pemanasan dalam menit (5–30 menit)');
            $table->timestamp('started_at')
                ->nullable()
                ->after('duration_minutes')
                ->comment('Waktu proses pemanasan dimulai');
        });
    }

    public function down(): void
    {
        Schema::table('thermal_logs', function (Blueprint $table) {
            $table->dropColumn(['duration_minutes', 'started_at']);
        });
    }
};
