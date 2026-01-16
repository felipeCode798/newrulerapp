<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoControversia extends Model
{
    protected $fillable = [
        'proceso_id',
        'categoria_controversia_id',
        'cedula',
        'valor_controversia',
        'fecha_hora_cita',
        'codigo_controversia',
        'venta_controversia',
        'documento_identidad',
        'poder',
    ];

    protected $casts = [
        'valor_controversia' => 'decimal:2',
        'venta_controversia' => 'decimal:2',
        'fecha_hora_cita' => 'datetime',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function categoriaControversia(): BelongsTo
    {
        return $this->belongsTo(CategoriaControversia::class);
    }
}
