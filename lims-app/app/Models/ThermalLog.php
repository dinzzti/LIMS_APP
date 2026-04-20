<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThermalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sample_id',
        'temperature_celsius',
        'duration_minutes',
        'started_at',
    ];

    protected $casts = [
        'temperature_celsius' => 'decimal:2',
        'started_at' => 'datetime',
    ];

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }
}