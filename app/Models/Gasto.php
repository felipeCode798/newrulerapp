<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    protected $fillable = [
        'descripcion',
        'valor',
        'proceso_id',
        'cia_id',
        'abogado_id',
        'tipo_pago',
        'estado',
        'fecha_gasto',
        'comprobante',
        'observaciones',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_gasto' => 'date',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function cia(): BelongsTo
    {
        return $this->belongsTo(Cia::class);
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(Abogado::class);
    }
}