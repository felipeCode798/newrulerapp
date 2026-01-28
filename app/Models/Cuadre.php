<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cuadre extends Model
{
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'total_pagos',
        'total_gastos',
        'diferencia',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'total_pagos' => 'decimal:2',
        'total_gastos' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}