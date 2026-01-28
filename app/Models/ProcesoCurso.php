<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoCurso extends Model
{
    protected $fillable = [
        'proceso_id',
        'curso_id',
        'nombre', // Asegúrate de tener este campo
        'numero_comparendo', // Asegúrate de tener este campo
        'cia_id', // Asegúrate de tener este campo
        'cedula',
        'porcentaje',
        'valor_transito',
        'valor_recibir',
        'estado', // Asegúrate de tener este campo
        'descripcion_general', // Asegúrate de tener este campo
    ];

    protected $casts = [
        'valor_transito' => 'decimal:2',
        'valor_recibir' => 'decimal:2',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    // ¡AGREGA ESTA RELACIÓN!
    public function cia(): BelongsTo
    {
        return $this->belongsTo(Cia::class);
    }
}