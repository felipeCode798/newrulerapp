<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoLicencia extends Model
{
    protected $fillable = [
        'proceso_id',
        'cedula',
        'categorias_seleccionadas',
        'escuela_id',
        'enrolamiento',
        'pin_escuela_id',
        'valor_carta_escuela',
        'examen_medico',
        'valor_examen_medico',
        'impresion',
        'valor_impresion',
        'sin_curso',
        'valor_sin_curso',
        'valor_total_licencia',
    ];

    protected $casts = [
        'categorias_seleccionadas' => 'array',
        'valor_carta_escuela' => 'decimal:2',
        'valor_examen_medico' => 'decimal:2',
        'valor_impresion' => 'decimal:2',
        'valor_sin_curso' => 'decimal:2',
        'valor_total_licencia' => 'decimal:2',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    public function pinEscuela(): BelongsTo
    {
        return $this->belongsTo(PinEscuela::class);
    }
}
