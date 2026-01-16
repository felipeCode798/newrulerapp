<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcesoRenovacion extends Model
{
    protected $table = 'proceso_renovaciones';

    protected $fillable = [
        'proceso_id',
        'renovaciones_seleccionadas',
        'cedula',
        'valor_total',
    ];

    protected $casts = [
        'renovaciones_seleccionadas' => 'array',
        'valor_total' => 'decimal:2',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }
}
