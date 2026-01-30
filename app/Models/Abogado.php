<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Abogado extends Model
{
    protected $fillable = [
        'nombre',
        'documento',
        // 'especialidad',
        // 'tarjeta_profesional',
        'telefono',
        'celular',
        'email',
        'direccion',
        // 'ciudad',
        'honorarios_hora',
        'porcentaje_comision',
        'areas_practica',
        'formacion_academica',
        'experiencia',
        'disponible',
        'activo',
    ];

    protected $casts = [
        // 'honorarios_hora' => 'decimal:2',
        // 'porcentaje_comision' => 'decimal:2',
        // 'disponible' => 'boolean',
        'activo' => 'boolean',
    ];

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
}