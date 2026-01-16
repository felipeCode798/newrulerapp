<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function tramitadores(): BelongsToMany
    {
        return $this->belongsToMany(Tramitador::class, 'tramitador_categoria')
            ->withPivot('precio')
            ->withTimestamps();
    }
}
