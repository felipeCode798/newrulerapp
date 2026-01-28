<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoTraspaso extends Model
{
    protected $fillable = [
        'proceso_id',
        'cedula',
        'nombre_propietario',
        'nombre_comprador',
        'cedula_comprador',
        'derecho_traspaso',
        'porcentaje',
        'honorarios',
        'comision',
        'total_recibir',
        'estado',
        'descripcion_general',
    ];

    protected $casts = [
        'derecho_traspaso' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'honorarios' => 'decimal:2',
        'comision' => 'decimal:2',
        'total_recibir' => 'decimal:2',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }
}
