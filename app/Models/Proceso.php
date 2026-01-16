<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proceso extends Model
{
    protected $fillable = [
        'tipo_usuario',
        'cliente_id',
        'tramitador_id',
        'total_general',
        'created_by',
    ];

    protected $casts = [
        'total_general' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tramitador(): BelongsTo
    {
        return $this->belongsTo(Tramitador::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(ProcesoCurso::class);
    }

    public function renovaciones(): HasMany
    {
        return $this->hasMany(ProcesoRenovacion::class);
    }

    public function licencias(): HasMany
    {
        return $this->hasMany(ProcesoLicencia::class);
    }

    public function traspasos(): HasMany
    {
        return $this->hasMany(ProcesoTraspaso::class);
    }

    public function runts(): HasMany
    {
        return $this->hasMany(ProcesoRunt::class);
    }

    public function controversias(): HasMany
    {
        return $this->hasMany(ProcesoControversia::class);
    }

    public function estadoCuentas(): HasMany
    {
        return $this->hasMany(EstadoCuenta::class);
    }
}
