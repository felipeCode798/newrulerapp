<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escuela extends Model
{
    protected $fillable = [
        'nombre',
        'numero_pines',
        'direccion',
        'telefono',
        'valor_carta_escuela',
        'activo',
    ];

    protected $casts = [
        'numero_pines' => 'integer',
        'valor_carta_escuela' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function pines(): HasMany
    {
        return $this->hasMany(PinEscuela::class);
    }
}
