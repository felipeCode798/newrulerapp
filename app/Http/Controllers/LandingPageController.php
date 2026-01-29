<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing-page');
    }
    
    public function buscarProcesos(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|max:20',
            'tipo' => 'required|in:cliente,tramitador',
        ]);
        
        $cedula = $request->cedula;
        $tipo = $request->tipo;
        
        if ($tipo === 'cliente') {
            // Buscar cliente por cédula
            $cliente = \App\Models\Cliente::where('cedula', $cedula)->first();
            
            if (!$cliente) {
                return back()->with('error', 'Cliente no encontrado');
            }
            
            // Buscar procesos del cliente
            $procesos = \App\Models\Proceso::where('tipo_usuario', 'cliente')
                ->where('cliente_id', $cliente->id)
                ->with(['cursos', 'renovaciones', 'licencias', 'traspasos', 'runts', 'controversias', 'pagos'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('portal.cliente', compact('cliente', 'procesos'));
            
        } else {
            // Buscar tramitador por cédula
            $tramitador = \App\Models\Tramitador::where('cedula', $cedula)->first();
            
            if (!$tramitador) {
                return back()->with('error', 'Tramitador no encontrado');
            }
            
            // Buscar procesos del tramitador
            $procesos = \App\Models\Proceso::where('tipo_usuario', 'tramitador')
                ->where('tramitador_id', $tramitador->id)
                ->with(['cursos', 'renovaciones', 'licencias', 'traspasos', 'runts', 'controversias', 'pagos'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('portal.tramitador', compact('tramitador', 'procesos'));
        }
    }
}