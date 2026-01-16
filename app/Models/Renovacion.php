<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Renovacion extends Model
{
    protected $table = 'renovaciones';

    protected $fillable = [
        'nombre',
        'tipo',
        'precio_cliente',
        'activo',
    ];

    protected $casts = [
        'precio_cliente' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function tramitadores(): BelongsToMany
    {
        return $this->belongsToMany(Tramitador::class, 'tramitador_renovacion')
            ->withPivot('precio_tramitador')
            ->withTimestamps();
    }
}
