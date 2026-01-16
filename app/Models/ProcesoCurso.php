<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoCurso extends Model
{
    protected $fillable = [
        'proceso_id',
        'curso_id',
        'cedula',
        'porcentaje',
        'valor_transito',
        'valor_recibir',
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
}
