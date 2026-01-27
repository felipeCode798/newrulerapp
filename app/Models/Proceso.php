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
        'tipo_servicio',       
        'descripcion_servicio'
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

    public function getCedulaCompletaAttribute(): string
    {
        if ($this->tipo_usuario === 'cliente' && $this->cliente) {
            return $this->cliente->cedula ?? '-';
        } elseif ($this->tipo_usuario === 'tramitador' && $this->tramitador) {
            return $this->tramitador->cedula ?? '-';
        }
        
        return '-';
    }

    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo_usuario === 'cliente' && $this->cliente) {
            return $this->cliente->nombre ?? '-';
        } elseif ($this->tipo_usuario === 'tramitador' && $this->tramitador) {
            return $this->tramitador->nombre ?? '-';
        }
        
        return '-';
    }

    // MODIFICA este método para evitar guardar si no hay cambios
    public function calcularTotalGeneral(): void
    {
        $total = 0;
        
        // Sumar cursos
        $total += $this->cursos()->sum('valor_recibir');
        
        // Sumar renovaciones
        $total += $this->renovaciones()->sum('valor_total');
        
        // Sumar licencias
        $total += $this->licencias()->sum('valor_total_licencia');
        
        // Sumar traspasos
        $total += $this->traspasos()->sum('total_recibir');
        
        // Sumar RUNT
        $total += $this->runts()->sum('valor_recibir');
        
        // Sumar controversias
        $total += $this->controversias()->sum('valor_controversia');
        
        $this->total_general = $total;
        $this->save();
    }
}