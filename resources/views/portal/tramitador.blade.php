<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Cliente - Sistema Jurídico de Tránsito</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-gavel text-2xl"></i>
                    <h1 class="text-2xl font-bold">Portal del Cliente</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-semibold">{{ $cliente->nombre }}</p>
                        <p class="text-sm text-blue-200">Cédula: {{ $cliente->cedula }}</p>
                    </div>
                    <a href="{{ route('landing.page') }}" class="bg-white text-blue-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Resumen -->
        <div class="mb-8 bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Resumen de Trámites
                </h2>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Última actualización</p>
                    <p class="font-semibold">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Procesos</p>
                            <p class="text-2xl font-bold">{{ $procesos->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Finalizados</p>
                            <p class="text-2xl font-bold">
                                {{ $procesos->filter(fn($p) => $p->estado_actual == 'finalizado')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">En Proceso</p>
                            <p class="text-2xl font-bold">
                                {{ $procesos->filter(fn($p) => $p->estado_actual == 'en_proceso')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pendientes</p>
                            <p class="text-2xl font-bold">
                                {{ $procesos->filter(fn($p) => $p->estado_actual == 'pendiente')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Procesos -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-list-alt text-blue-600 mr-2"></i>
                    Mis Trámites
                </h2>
            </div>
            
            @if($procesos->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No hay trámites registrados</h3>
                    <p class="text-gray-500">No se encontraron procesos asociados a esta cédula</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID / Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo de Servicio
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descripción
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total / Pagos
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Detalles
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($procesos as $proceso)
                                @php
                                    $estadoBadge = match($proceso->estado_actual) {
                                        'finalizado' => 'bg-green-100 text-green-800',
                                        'en_proceso' => 'bg-yellow-100 text-yellow-800',
                                        'enviado' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                    
                                    $estadoText = match($proceso->estado_actual) {
                                        'finalizado' => 'Finalizado',
                                        'en_proceso' => 'En Proceso',
                                        'enviado' => 'Enviado',
                                        default => 'Pendiente',
                                    };
                                    
                                    $pagos = \App\Models\Pago::calcularSaldoProceso($proceso);
                                @endphp
                                
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            #{{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $proceso->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            @switch($proceso->tipo_servicio)
                                                @case('curso')
                                                    <i class="fas fa-graduation-cap text-blue-600 mr-2"></i> Curso
                                                    @break
                                                @case('renovacion')
                                                    <i class="fas fa-sync-alt text-green-600 mr-2"></i> Renovación
                                                    @break
                                                @case('licencia')
                                                    <i class="fas fa-id-card text-purple-600 mr-2"></i> Licencia
                                                    @break
                                                @case('controversia')
                                                    <i class="fas fa-balance-scale text-red-600 mr-2"></i> Controversia
                                                    @break
                                                @case('traspaso')
                                                    <i class="fas fa-exchange-alt text-yellow-600 mr-2"></i> Traspaso
                                                    @break
                                                @case('runt')
                                                    <i class="fas fa-car text-indigo-600 mr-2"></i> RUNT
                                                    @break
                                                @default
                                                    <i class="fas fa-file-alt text-gray-600 mr-2"></i> {{ ucfirst($proceso->tipo_servicio) }}
                                            @endswitch
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $proceso->descripcion_servicio }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($proceso->cursos->count() > 0)
                                                {{ $proceso->cursos->count() }} curso(s)
                                            @elseif($proceso->renovaciones->count() > 0)
                                                {{ $proceso->renovaciones->count() }} renovación(es)
                                            @elseif($proceso->licencias->count() > 0)
                                                {{ $proceso->licencias->count() }} licencia(s)
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoBadge }}">
                                            {{ $estadoText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            ${{ number_format($proceso->total_general, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Pagado: ${{ number_format($pagos['total_pagado'], 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="mostrarDetalle({{ $proceso->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i> Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Detalles del proceso (hidden por defecto) -->
                                <tr id="detalle-{{ $proceso->id }}" class="hidden bg-blue-50">
                                    <td colspan="6" class="px-6 py-4">
                                        <div class="bg-white rounded-lg p-4 shadow-sm">
                                            <h4 class="font-bold text-lg mb-3 text-gray-800">Detalles del Trámite #{{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }}</h4>
                                            
                                            <!-- Servicios específicos -->
                                            @if($proceso->cursos->count() > 0)
                                                <div class="mb-4">
                                                    <h5 class="font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-graduation-cap text-blue-600 mr-2"></i> Cursos:
                                                    </h5>
                                                    @foreach($proceso->cursos as $curso)
                                                        <div class="ml-4 mb-2 p-3 bg-gray-50 rounded-lg">
                                                            <div class="flex justify-between">
                                                                <div>
                                                                    <p class="font-medium">{{ $curso->curso->categoria ?? 'Curso' }}</p>
                                                                    <p class="text-sm text-gray-600">Cédula: {{ $curso->cedula }}</p>
                                                                </div>
                                                                <div class="text-right">
                                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                                        {{ $curso->estado == 'finalizado' ? 'bg-green-100 text-green-800' : 
                                                                           ($curso->estado == 'en_proceso' ? 'bg-yellow-100 text-yellow-800' : 
                                                                           'bg-gray-100 text-gray-800') }}">
                                                                        {{ ucfirst($curso->estado) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            @if($proceso->renovaciones->count() > 0)
                                                <div class="mb-4">
                                                    <h5 class="font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-sync-alt text-green-600 mr-2"></i> Renovaciones:
                                                    </h5>
                                                    @foreach($proceso->renovaciones as $renovacion)
                                                        <div class="ml-4 mb-2 p-3 bg-gray-50 rounded-lg">
                                                            <div class="flex justify-between">
                                                                <div>
                                                                    <p class="font-medium">{{ $renovacion->renovacion->nombre ?? 'Renovación' }}</p>
                                                                    <p class="text-sm text-gray-600">Cédula: {{ $renovacion->cedula }}</p>
                                                                </div>
                                                                <div class="text-right">
                                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                                        {{ $renovacion->estado == 'finalizado' ? 'bg-green-100 text-green-800' : 
                                                                           ($renovacion->estado == 'en_proceso' ? 'bg-yellow-100 text-yellow-800' : 
                                                                           'bg-gray-100 text-gray-800') }}">
                                                                        {{ ucfirst($renovacion->estado) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <!-- Historial de pagos -->
                                            @if($proceso->pagos->count() > 0)
                                                <div class="mt-4">
                                                    <h5 class="font-semibold text-gray-700 mb-2">
                                                        <i class="fas fa-receipt text-purple-600 mr-2"></i> Historial de Pagos:
                                                    </h5>
                                                    <div class="ml-4">
                                                        <table class="min-w-full divide-y divide-gray-200">
                                                            <thead class="bg-gray-100">
                                                                <tr>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Fecha</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Método</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Valor</th>
                                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Referencia</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="bg-white divide-y divide-gray-200">
                                                                @foreach($proceso->pagos as $pago)
                                                                    <tr>
                                                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                                            {{ $pago->fecha_pago->format('d/m/Y') }}
                                                                        </td>
                                                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                                                            {{ ucfirst($pago->metodo) }}
                                                                        </td>
                                                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                                                            ${{ number_format($pago->valor, 0, ',', '.') }}
                                                                        </td>
                                                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500">
                                                                            {{ $pago->referencia ?? '-' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript para mostrar/ocultar detalles -->
    <script>
        function mostrarDetalle(procesoId) {
            const detalle = document.getElementById(`detalle-${procesoId}`);
            detalle.classList.toggle('hidden');
            
            const boton = event.target.closest('button');
            if (detalle.classList.contains('hidden')) {
                boton.innerHTML = '<i class="fas fa-eye mr-1"></i> Ver Detalles';
            } else {
                boton.innerHTML = '<i class="fas fa-eye-slash mr-1"></i> Ocultar Detalles';
            }
        }
        
        // Función para imprimir
        function imprimirResumen() {
            window.print();
        }
    </script>
</body>
</html>