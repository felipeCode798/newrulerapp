<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoControversia extends Model
{
    protected $fillable = [
        'proceso_id',
        'categoria_controversia_id',
        'nombre',
        'comparendo',
        'cia_id',
        'precio_cia',
        'cedula',
        'celular',
        'debe',
        'valor_controversia',
        'fecha_hora_cita',
        'codigo_controversia',
        'venta_controversia',
        'documento_identidad',
        'poder',
        'estado',
        'descripcion_general',
    ];

    protected $casts = [
        'valor_controversia' => 'decimal:2',
        'venta_controversia' => 'decimal:2',
        'precio_cia' => 'decimal:2',
        'fecha_hora_cita' => 'datetime',
        'debe' => 'boolean',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    // ¡ESTA RELACIÓN ES LA QUE FALTA!
    public function categoriaControversia(): BelongsTo
    {
        return $this->belongsTo(CategoriaControversia::class);
    }

    public function cia(): BelongsTo
    {
        return $this->belongsTo(Cia::class);
    }
}