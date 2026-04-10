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
    ];

    protected $casts = [
        'temperature_celsius' => 'decimal:2',
    ];

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }
}