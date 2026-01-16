<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoRunt extends Model
{
    protected $fillable = [
        'proceso_id',
        'nombre',
        'cedula',
        'numero',
        'comision',
        'pago',
        'honorarios',
        'valor_recibir',
    ];

    protected $casts = [
        'comision' => 'decimal:2',
        'pago' => 'decimal:2',
        'honorarios' => 'decimal:2',
        'valor_recibir' => 'decimal:2',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }
}
