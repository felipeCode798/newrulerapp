<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoRenovacion extends Model
{
    protected $table = 'proceso_renovaciones';

    protected $fillable = [
        'proceso_id',
        'renovacion_id',
        'nombre',
        'cedula',
        'incluye_examen',
        'incluye_lamina',
        'valor_total',
        'estado',
        'descripcion_general',
    ];

    protected $casts = [
        'incluye_examen' => 'boolean',
        'incluye_lamina' => 'boolean',
        'valor_total' => 'decimal:2',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function renovacion(): BelongsTo
    {
        return $this->belongsTo(Renovacion::class);
    }
}