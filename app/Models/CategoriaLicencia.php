<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaLicencia extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'examen_medico',
        'lamina',
        'honorarios',
        'sin_curso',
        'activo',
    ];

    protected $casts = [
        'examen_medico' => 'decimal:2',
        'lamina' => 'decimal:2',
        'honorarios' => 'decimal:2',
        'sin_curso' => 'decimal:2',
        'activo' => 'boolean',
    ];
}
