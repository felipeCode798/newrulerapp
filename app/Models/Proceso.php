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
        } elseif ($this->tipo_usuario === 'tramitador') {
            // Para tramitador, mostrar la cédula del beneficiario
            return $this->obtenerCedulaBeneficiario();
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

    /**
     * Obtiene la cédula del beneficiario cuando el proceso es de un tramitador
     */
    private function obtenerCedulaBeneficiario(): string
    {
        // Cargar las relaciones necesarias si no están cargadas
        $this->loadMissing([
            'cursos' => fn($q) => $q->select('proceso_id', 'cedula'),
            'renovaciones' => fn($q) => $q->select('proceso_id', 'cedula'),
            'licencias' => fn($q) => $q->select('proceso_id', 'cedula'),
            'traspasos' => fn($q) => $q->select('proceso_id', 'cedula'),
            'runts' => fn($q) => $q->select('proceso_id', 'cedula'),
            'controversias' => fn($q) => $q->select('proceso_id', 'cedula')
        ]);

        // Buscar en todas las relaciones posibles la cédula del beneficiario
        
        // Buscar en cursos
        if ($this->cursos->isNotEmpty()) {
            $curso = $this->cursos->first();
            if ($curso && $curso->cedula) {
                return $curso->cedula;
            }
        }
        
        // Buscar en renovaciones
        if ($this->renovaciones->isNotEmpty()) {
            $renovacion = $this->renovaciones->first();
            if ($renovacion && $renovacion->cedula) {
                return $renovacion->cedula;
            }
        }
        
        // Buscar en licencias
        if ($this->licencias->isNotEmpty()) {
            $licencia = $this->licencias->first();
            if ($licencia && $licencia->cedula) {
                return $licencia->cedula;
            }
        }
        
        // Buscar en traspasos
        if ($this->traspasos->isNotEmpty()) {
            $traspaso = $this->traspasos->first();
            if ($traspaso && $traspaso->cedula) {
                return $traspaso->cedula;
            }
        }
        
        // Buscar en RUNTS
        if ($this->runts->isNotEmpty()) {
            $runt = $this->runts->first();
            if ($runt && $runt->cedula) {
                return $runt->cedula;
            }
        }
        
        // Buscar en controversias
        if ($this->controversias->isNotEmpty()) {
            $controversia = $this->controversias->first();
            if ($controversia && $controversia->cedula) {
                return $controversia->cedula;
            }
        }
        
        // Si no se encuentra ninguna cédula, mostrar la del tramitador
        return $this->tramitador->cedula ?? '-';
    }

    /**
     * Método auxiliar para obtener el nombre del beneficiario
     */
    public function getNombreBeneficiarioAttribute(): string
    {
        if ($this->tipo_usuario === 'cliente' && $this->cliente) {
            return $this->cliente->nombre ?? '-';
        } elseif ($this->tipo_usuario === 'tramitador') {
            // Para tramitador, buscar en los registros específicos
            switch ($this->tipo_servicio) {
                case 'curso':
                    if ($this->cursos()->exists()) {
                        $curso = $this->cursos()->first();
                        if ($curso && $curso->cedula) {
                            return "Cliente cédula: {$curso->cedula}";
                        }
                    }
                    break;
                    
                case 'renovacion':
                    if ($this->renovaciones()->exists()) {
                        $renovacion = $this->renovaciones()->first();
                        if ($renovacion && $renovacion->cedula) {
                            return "Cliente cédula: {$renovacion->cedula}";
                        }
                    }
                    break;
                    
                case 'traspaso':
                    if ($this->traspasos()->exists()) {
                        $traspaso = $this->traspasos()->first();
                        if ($traspaso) {
                            return $traspaso->nombre_propietario ?? "Cliente cédula: {$traspaso->cedula}";
                        }
                    }
                    break;
                    
                case 'runt':
                    if ($this->runts()->exists()) {
                        $runt = $this->runts()->first();
                        if ($runt) {
                            return $runt->nombre ?? "Cliente cédula: {$runt->cedula}";
                        }
                    }
                    break;
            }
            
            return "Tramitador: {$this->tramitador->nombre}";
        }
        
        return '-';
    }

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