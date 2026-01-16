<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaControversia extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'precio_cliente',
        'activo',
    ];

    protected $casts = [
        'precio_cliente' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function tramitadores(): BelongsToMany
    {
        return $this->belongsToMany(Tramitador::class, 'tramitador_controversia')
            ->withPivot('precio_tramitador')
            ->withTimestamps();
    }
}
