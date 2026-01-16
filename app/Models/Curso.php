<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $fillable = [
        'codigo',
        'categoria',
        'precio_cliente_50_transito',
        'precio_cliente_50_recibir',
        'precio_cliente_20_transito',
        'precio_cliente_20_recibir',
        'activo',
    ];

    protected $casts = [
        'precio_cliente_50_transito' => 'decimal:2',
        'precio_cliente_50_recibir' => 'decimal:2',
        'precio_cliente_20_transito' => 'decimal:2',
        'precio_cliente_20_recibir' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
