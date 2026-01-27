<?php

namespace App\Filament\Resources\ProcesoResource\Pages;

use App\Filament\Resources\ProcesoResource;
use App\Models\EstadoCuenta;
use App\Models\Proceso;
use App\Models\ProcesoCurso;
use App\Models\ProcesoRenovacion;
use App\Models\ProcesoLicencia;
use App\Models\ProcesoTraspaso;
use App\Models\ProcesoRunt;
use App\Models\ProcesoControversia;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProceso extends CreateRecord
{
    protected static string $resource = ProcesoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        
        // Determinar automáticamente el tipo de servicio basado en lo que se está agregando
        if (isset($data['cursos']) && count($data['cursos']) > 0) {
            $data['tipo_servicio'] = 'curso';
            $data['descripcion_servicio'] = 'Curso';
        } elseif (isset($data['renovaciones']) && count($data['renovaciones']) > 0) {
            $data['tipo_servicio'] = 'renovacion';
            $data['descripcion_servicio'] = 'Renovación';
        } elseif (isset($data['licencias']) && count($data['licencias']) > 0) {
            $data['tipo_servicio'] = 'licencia';
            $data['descripcion_servicio'] = 'Licencia';
        } elseif (isset($data['traspasos']) && count($data['traspasos']) > 0) {
            $data['tipo_servicio'] = 'traspaso';
            $data['descripcion_servicio'] = 'Traspaso';
        } elseif (isset($data['runts']) && count($data['runts']) > 0) {
            $data['tipo_servicio'] = 'runt';
            $data['descripcion_servicio'] = 'RUNT';
        } elseif (isset($data['controversias']) && count($data['controversias']) > 0) {
            $data['tipo_servicio'] = 'controversia';
            $data['descripcion_servicio'] = 'Controversia';
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Crear procesos individuales para cada servicio
        $procesosCreados = $this->crearProcesosIndividuales();
        
        // Eliminar el proceso "maestro" que se creó automáticamente
        $this->record->delete();
        
        // Mostrar notificación de éxito
        $count = count($procesosCreados);
        
        if ($count > 0) {
            Notification::make()
                ->title('¡Procesos creados exitosamente!')
                ->body("Se han creado {$count} procesos individuales.")
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        // Redirigir a la tabla de procesos (index) en lugar de edit
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return null; // Ya mostramos nuestra propia notificación
    }

    private function crearProcesosIndividuales(): array
    {
        $procesosCreados = [];
        $data = $this->data;
        
        // 1. Crear procesos para cursos
        if (isset($data['cursos']) && is_array($data['cursos'])) {
            foreach ($data['cursos'] as $cursoData) {
                if (!empty($cursoData['curso_id'])) {
                    $curso = \App\Models\Curso::find($cursoData['curso_id']);
                    $nombreCurso = $curso ? $curso->categoria : 'Curso';
                    
                    $proceso = $this->crearProcesoBase($data, 'curso', 'Curso: ' . $nombreCurso);
                    
                    // Crear el curso relacionado
                    ProcesoCurso::create([
                        'proceso_id' => $proceso->id,
                        'curso_id' => $cursoData['curso_id'],
                        'cedula' => $cursoData['cedula'] ?? $this->obtenerCedulaBase($data),
                        'porcentaje' => $cursoData['porcentaje'] ?? '50',
                        'valor_transito' => $cursoData['valor_transito'] ?? 0,
                        'valor_recibir' => $cursoData['valor_recibir'] ?? 0,
                    ]);
                    
                    $proceso->calcularTotalGeneral();
                    $procesosCreados[] = $proceso;
                }
            }
        }
        
        // 2. Crear procesos para renovaciones
        if (isset($data['renovaciones']) && is_array($data['renovaciones'])) {
            foreach ($data['renovaciones'] as $renovacionData) {
                if (!empty($renovacionData['renovacion_id'])) {
                    $renovacion = \App\Models\Renovacion::find($renovacionData['renovacion_id']);
                    $nombreRenovacion = $renovacion ? $renovacion->nombre : 'Renovación';
                    
                    $proceso = $this->crearProcesoBase($data, 'renovacion', 'Renovación: ' . $nombreRenovacion);
                    
                    // Crear la renovación relacionada
                    ProcesoRenovacion::create([
                        'proceso_id' => $proceso->id,
                        'renovacion_id' => $renovacionData['renovacion_id'],
                        'cedula' => $renovacionData['cedula'] ?? $this->obtenerCedulaBase($data),
                        'incluye_examen' => $renovacionData['incluye_examen'] ?? true,
                        'incluye_lamina' => $renovacionData['incluye_lamina'] ?? true,
                        'valor_total' => $renovacionData['valor_total'] ?? 0,
                    ]);
                    
                    $proceso->calcularTotalGeneral();
                    $procesosCreados[] = $proceso;
                }
            }
        }
        
        // 3. Crear procesos para licencias
        if (isset($data['licencias']) && is_array($data['licencias'])) {
            foreach ($data['licencias'] as $licenciaData) {
                if (!empty($licenciaData['categorias_seleccionadas'])) {
                    $descripcion = 'Licencia';
                    if (!empty($licenciaData['categorias_seleccionadas'])) {
                        $categorias = \App\Models\CategoriaLicencia::whereIn('id', $licenciaData['categorias_seleccionadas'])
                            ->pluck('nombre')
                            ->toArray();
                        $descripcion = 'Licencia: ' . implode(', ', $categorias);
                    }
                    
                    $proceso = $this->crearProcesoBase($data, 'licencia', $descripcion);
                    
                    // Crear la licencia relacionada
                    ProcesoLicencia::create([
                        'proceso_id' => $proceso->id,
                        'cedula' => $licenciaData['cedula'] ?? $this->obtenerCedulaBase($data),
                        'categorias_seleccionadas' => $licenciaData['categorias_seleccionadas'] ?? [],
                        'escuela_id' => $licenciaData['escuela_id'] ?? null,
                        'enrolamiento' => $licenciaData['enrolamiento'] ?? 'guardado',
                        'pin_escuela_id' => $licenciaData['pin_escuela_id'] ?? null,
                        'valor_carta_escuela' => $licenciaData['valor_carta_escuela'] ?? 0,
                        'examen_medico' => $licenciaData['examen_medico'] ?? 'no_aplica',
                        'valor_examen_medico' => $licenciaData['valor_examen_medico'] ?? 0,
                        'impresion' => $licenciaData['impresion'] ?? 'no_aplica',
                        'valor_impresion' => $licenciaData['valor_impresion'] ?? 0,
                        'sin_curso' => $licenciaData['sin_curso'] ?? 'no_aplica',
                        'valor_sin_curso' => $licenciaData['valor_sin_curso'] ?? 0,
                        'valor_total_licencia' => $licenciaData['valor_total_licencia'] ?? 0,
                    ]);
                    
                    $proceso->calcularTotalGeneral();
                    $procesosCreados[] = $proceso;
                }
            }
        }
        
        // 4. Crear procesos para traspasos
        if (isset($data['traspasos']) && is_array($data['traspasos'])) {
            foreach ($data['traspasos'] as $traspasoData) {
                if (!empty($traspasoData['nombre_propietario'])) {
                    $proceso = $this->crearProcesoBase($data, 'traspaso', 'Traspaso: ' . $traspasoData['nombre_propietario']);
                    
                    // Crear el traspaso relacionado
                    ProcesoTraspaso::create([
                        'proceso_id' => $proceso->id,
                        'cedula' => $traspasoData['cedula'] ?? $this->obtenerCedulaBase($data),
                        'nombre_propietario' => $traspasoData['nombre_propietario'],
                        'nombre_comprador' => $traspasoData['nombre_comprador'],
                        'cedula_comprador' => $traspasoData['cedula_comprador'],
                        'derecho_traspaso' => $traspasoData['derecho_traspaso'] ?? 0,
                        'porcentaje' => $traspasoData['porcentaje'] ?? 0,
                        'honorarios' => $traspasoData['honorarios'] ?? 0,
                        'comision' => $traspasoData['comision'] ?? 0,
                        'total_recibir' => $traspasoData['total_recibir'] ?? 0,
                    ]);
                    
                    $proceso->calcularTotalGeneral();
                    $procesosCreados[] = $proceso;
                }
            }
        }
        
        // 5. Crear procesos para RUNT
        if (isset($data['runts']) && is_array($data['runts'])) {
            foreach ($data['runts'] as $runtData) {
                if (!empty($runtData['nombre'])) {
                    $proceso = $this->crearProcesoBase($data, 'runt', 'RUNT: ' . $runtData['nombre']);
                    
                    // Crear el RUNT relacionado
                    ProcesoRunt::create([
                        'proceso_id' => $proceso->id,
                        'nombre' => $runtData['nombre'],
                        'cedula' => $runtData['cedula'] ?? $this->obtenerCedulaBase($data),
                        'numero' => $runtData['numero'],
                        'comision' => $runtData['comision'] ?? 0,
                        'pago' => $runtData['pago'] ?? 0,
                        'honorarios' => $runtData['honorarios'] ?? 0,
                        'valor_recibir' => $runtData['valor_recibir'] ?? 0,
                    ]);
                    
                    $proceso->calcularTotalGeneral();
                    $procesosCreados[] = $proceso;
                }
            }
        }
        
        // 6. Crear procesos para controversias
        if (isset($data['controversias']) && is_array($data['controversias'])) {
            foreach ($data['controversias'] as $controversiaData) {
                if (!empty($controversiaData['categoria_controversia_id'])) {
                    $categoria = \App\Models\CategoriaControversia::find($controversiaData['categoria_controversia_id']);
                    $nombreCategoria = $categoria ? $categoria->nombre : 'Controversia';
                    
                    $proceso = $this->crearProcesoBase($data, 'controversia', 'Controversia: ' . $nombreCategoria);
                    
                    // Crear la controversia relacionada
                    ProcesoControversia::create([
                        'proceso_id' => $proceso->id,
                        'categoria_controversia_id' => $controversiaData['categoria_controversia_id'],
                        'cedula' => $controversiaData['cedula'] ?? $this->obtenerCedulaBase($data),
                        'valor_controversia' => $controversiaData['valor_controversia'] ?? 0,
                        'fecha_hora_cita' => $controversiaData['fecha_hora_cita'],
                        'codigo_controversia' => $controversiaData['codigo_controversia'],
                        'venta_controversia' => $controversiaData['venta_controversia'] ?? 0,
                        'documento_identidad' => $controversiaData['documento_identidad'] ?? null,
                        'poder' => $controversiaData['poder'] ?? null,
                    ]);
                    
                    $proceso->calcularTotalGeneral();
                    $procesosCreados[] = $proceso;
                }
            }
        }
        
        // 7. Estados de cuenta (se asignan al primer proceso creado)
        if (isset($data['estados_cuenta']) && is_array($data['estados_cuenta']) && count($procesosCreados) > 0) {
            $primerProceso = $procesosCreados[0];
            foreach ($data['estados_cuenta'] as $archivo) {
                EstadoCuenta::create([
                    'proceso_id' => $primerProceso->id,
                    'archivo' => $archivo,
                ]);
            }
        }
        
        return $procesosCreados;
    }
    
    private function crearProcesoBase(array $data, string $tipoServicio, string $descripcion): Proceso
    {
        return Proceso::create([
            'tipo_usuario' => $data['tipo_usuario'],
            'cliente_id' => $data['tipo_usuario'] === 'cliente' ? $data['cliente_id'] : null,
            'tramitador_id' => $data['tipo_usuario'] === 'tramitador' ? $data['tramitador_id'] : null,
            'tipo_servicio' => $tipoServicio,
            'descripcion_servicio' => $descripcion,
            'total_general' => 0,
            'created_by' => Auth::id(),
        ]);
    }
    
    private function obtenerCedulaBase(array $data): string
    {
        if ($data['tipo_usuario'] === 'cliente' && isset($data['cliente_cedula_base'])) {
            return $data['cliente_cedula_base'];
        }
        return '';
    }

    private function obtenerNombreCurso($cursoId): string
    {
        $curso = \App\Models\Curso::find($cursoId);
        return $curso ? $curso->categoria : 'Curso';
    }
    
    private function obtenerNombreRenovacion($renovacionId): string
    {
        $renovacion = \App\Models\Renovacion::find($renovacionId);
        return $renovacion ? $renovacion->nombre : 'Renovación';
    }
}