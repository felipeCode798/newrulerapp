<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Renovacion extends Model
{
    protected $table = 'renovaciones';

    protected $fillable = [
        'nombre',
        'precio_renovacion',
        'precio_examen',
        'precio_lamina',
        'activo',
    ];

    protected $casts = [
        'precio_renovacion' => 'decimal:2',
        'precio_examen' => 'decimal:2',
        'precio_lamina' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function tramitadores(): BelongsToMany
    {
        return $this->belongsToMany(Tramitador::class, 'tramitador_renovacion')
            ->withPivot([
                'precio_renovacion',
                'precio_examen',
                'precio_lamina'
            ])
            ->withTimestamps();
    }
}