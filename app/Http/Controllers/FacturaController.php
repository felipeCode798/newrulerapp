<?php

namespace App\Http\Controllers;

use App\Models\Proceso;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function generarPDF($id)
    {
        $proceso = Proceso::with(['cursos.curso', 'renovaciones.renovacion', 'licencias.escuela', 'traspasos', 'runts', 'controversias.categoriaControversia'])->findOrFail($id);
        
        // Generar contenido HTML (similar al que ya tienes)
        $html = view('facturas.pdf', compact('proceso'))->render();
        
        // Usar Dompdf (deberías tenerlo instalado)
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        
        return $pdf->download("factura-{$id}.pdf");
    }
    
    public function verPDF($id)
    {
        $proceso = Proceso::with(['cursos.curso', 'renovaciones.renovacion', 'licencias.escuela', 'traspasos', 'runts', 'controversias.categoriaControversia'])->findOrFail($id);
        
        $html = view('facturas.pdf', compact('proceso'))->render();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
        
        return $pdf->stream("factura-{$id}.pdf");
    }
}