<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cia extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'direccion',
        'telefono',
        'email',
        'contacto',
        'celular_contacto',
        'observaciones',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function precios(): HasMany
    {
        return $this->hasMany(CiaPrecio::class);
    }

    public function procesosCursos(): HasMany
    {
        return $this->hasMany(ProcesoCurso::class);
    }

    public function procesosControversias(): HasMany
    {
        return $this->hasMany(ProcesoControversia::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
}