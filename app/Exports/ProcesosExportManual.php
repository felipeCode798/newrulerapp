<?php

namespace App\Exports;

use App\Models\Proceso;

class ProcesosExportManual
{
    protected $procesos;

    public function __construct($procesos)
    {
        $this->procesos = $procesos;
    }

    public function download($filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() {
            // Crear un manejador de salida
            $handle = fopen('php://output', 'w');
            
            // Agregar BOM para Excel en UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Encabezados
            fputcsv($handle, [
                'ID',
                'Fecha',
                'Tipo',
                'Cliente',
                'Cédula Cliente',
                'Tramitador',
                'Cédula Tramitador',
                'Total Cursos',
                'Total Renovaciones',
                'Total Licencias',
                'Total Traspasos',
                'Total RUNT',
                'Total Controversias',
                'TOTAL GENERAL',
                'Creado Por',
            ], ';');
            
            // Datos
            foreach ($this->procesos as $proceso) {
                fputcsv($handle, [
                    $proceso->id,
                    $proceso->created_at->format('d/m/Y H:i'),
                    $proceso->tipo_usuario == 'cliente' ? 'Cliente' : 'Tramitador',
                    $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->nombre ?? '-' : '-',
                    $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->cedula ?? '-' : '-',
                    $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->nombre ?? '-' : '-',
                    $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->cedula ?? '-' : '-',
                    number_format($proceso->cursos()->sum('valor_recibir'), 2, ',', '.'),
                    number_format($proceso->renovaciones()->sum('valor_total'), 2, ',', '.'),
                    number_format($proceso->licencias()->sum('valor_total_licencia'), 2, ',', '.'),
                    number_format($proceso->traspasos()->sum('total_recibir'), 2, ',', '.'),
                    number_format($proceso->runts()->sum('valor_recibir'), 2, ',', '.'),
                    number_format($proceso->controversias()->sum('valor_controversia'), 2, ',', '.'),
                    number_format($proceso->total_general, 2, ',', '.'),
                    $proceso->createdBy->name ?? '-',
                ], ';');
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}