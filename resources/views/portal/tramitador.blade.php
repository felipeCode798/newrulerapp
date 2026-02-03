<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal del Tramitador - Sistema Jurídico de Tránsito</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #dc2626;    /* Rojo principal */
            --secondary-red: #ef4444;   /* Rojo secundario */
            --dark-bg: #000000;        /* Fondo negro */
            --card-bg: #1f1f1f;        /* Fondo tarjetas */
            --light-gray: #d1d5db;     /* Gris claro para texto */
        }
        
        body {
            background-color: var(--dark-bg);
            color: #ffffff;
        }
        
        /* Estilos para estados de procesos */
        .estado-finalizado {
            background-color: rgba(21, 128, 61, 0.2); /* Verde oscuro */
            color: #86efac;
            border-color: #065f46;
        }
        
        .estado-proceso {
            background-color: rgba(251, 191, 36, 0.2); /* Amarillo oscuro */
            color: #fde047;
            border-color: #92400e;
        }
        
        .estado-pendiente {
            background-color: rgba(220, 38, 38, 0.2); /* Rojo oscuro */
            color: #fca5a5;
            border-color: #7f1d1d;
        }
        
        .estado-enviado {
            background-color: rgba(59, 130, 246, 0.2); /* Azul oscuro */
            color: #93c5fd;
            border-color: #1e3a8a;
        }
        
        /* Estilos para tarjetas de resumen */
        .card-total {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
            border: 1px solid #dc2626;
        }
        
        .card-finalizado {
            background: linear-gradient(135deg, #1f1f1f 0%, #1a2c1a 100%);
            border: 1px solid #16a34a;
        }
        
        .card-proceso {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a261a 100%);
            border: 1px solid #d97706;
        }
        
        .card-pendiente {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a1a1a 100%);
            border: 1px solid #dc2626;
        }
        
        /* Iconos de tarjetas */
        .icon-total {
            background: rgba(220, 38, 38, 0.2);
            color: #f87171;
        }
        
        .icon-finalizado {
            background: rgba(21, 128, 61, 0.2);
            color: #4ade80;
        }
        
        .icon-proceso {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }
        
        .icon-pendiente {
            background: rgba(220, 38, 38, 0.2);
            color: #f87171;
        }
        
        /* Estilos para diferentes tipos de servicios */
        .badge-curso {
            background: rgba(59, 130, 246, 0.2);
            color: #93c5fd;
            border-color: #1e3a8a;
        }
        
        .badge-renovacion {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
            border-color: #065f46;
        }
        
        .badge-licencia {
            background: rgba(168, 85, 247, 0.2);
            color: #d8b4fe;
            border-color: #6b21a8;
        }
        
        .badge-controversia {
            background: rgba(245, 158, 11, 0.2);
            color: #fde047;
            border-color: #92400e;
        }
        
        .badge-traspaso {
            background: rgba(14, 165, 233, 0.2);
            color: #7dd3fc;
            border-color: #0369a1;
        }
        
        .badge-runt {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border-color: #991b1b;
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Navbar -->
    <nav class="bg-gray-900 shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/Logo_ruler.png') }}" alt="Ruler Soluciones" class="h-16 w-auto">
                    <div class="border-l border-gray-700 pl-4">
                        <h1 class="text-2xl font-bold">Portal del Tramitador</h1>
                        <p class="text-sm text-gray-400">Gestión de procesos asignados</p>
                    </div>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="font-semibold text-lg">{{ $tramitador->nombre }}</p>
                        <p class="text-sm text-gray-400">Cédula: {{ $tramitador->cedula }}</p>
                        <p class="text-sm text-gray-400">Tel: {{ $tramitador->telefono }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('landing.page') }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg font-semibold transition duration-300 shadow-md">
                            <i class="fas fa-home mr-2"></i> Inicio
                        </a>
                        <button onclick="imprimirResumen()" 
                                class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-300 border border-gray-700">
                            <i class="fas fa-print mr-2"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Resumen -->
        <div class="mb-8 bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-800">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-chart-line text-red-500 mr-2"></i>
                        Resumen de Procesos Gestionados
                    </h2>
                    <p class="text-gray-400 mt-1">Todos los procesos que tienes asignados</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-400">Última actualización</p>
                    <p class="font-semibold text-lg text-red-400">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="card-total p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="icon-total p-3 rounded-lg mr-4">
                            <i class="fas fa-clipboard-list text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Total Procesos</p>
                            <p class="text-2xl font-bold">{{ $procesos->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-finalizado p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="icon-finalizado p-3 rounded-lg mr-4">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Finalizados</p>
                            <p class="text-2xl font-bold">
                                {{ $procesos->filter(fn($p) => $p->estado_actual == 'finalizado')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card-proceso p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="icon-proceso p-3 rounded-lg mr-4">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">En Proceso</p>
                            <p class="text-2xl font-bold">
                                {{ $procesos->filter(fn($p) => $p->estado_actual == 'en_proceso')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="card-pendiente p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="icon-pendiente p-3 rounded-lg mr-4">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Pendientes</p>
                            <p class="text-2xl font-bold">
                                {{ $procesos->filter(fn($p) => $p->estado_actual == 'pendiente')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700">
                    <div class="flex items-center">
                        <div class="bg-purple-500/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-money-bill-wave text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Ingresos Totales</p>
                            <p class="text-2xl font-bold text-green-400">
                                ${{ number_format($procesos->sum('total_general'), 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumen por tipo de servicio -->
            <div class="mt-6 pt-6 border-t border-gray-800">
                <h3 class="text-lg font-bold mb-4 text-gray-300">
                    <i class="fas fa-chart-pie mr-2"></i> Distribución por Tipo de Servicio
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                    @php
                        $tipos = [
                            'curso' => ['icon' => 'fa-graduation-cap', 'color' => 'badge-curso', 'text' => 'Cursos'],
                            'renovacion' => ['icon' => 'fa-sync-alt', 'color' => 'badge-renovacion', 'text' => 'Renovaciones'],
                            'licencia' => ['icon' => 'fa-id-card', 'color' => 'badge-licencia', 'text' => 'Licencias'],
                            'controversia' => ['icon' => 'fa-balance-scale', 'color' => 'badge-controversia', 'text' => 'Controversias'],
                            'traspaso' => ['icon' => 'fa-exchange-alt', 'color' => 'badge-traspaso', 'text' => 'Traspasos'],
                            'runt' => ['icon' => 'fa-car', 'color' => 'badge-runt', 'text' => 'RUNT'],
                        ];
                    @endphp
                    
                    @foreach($tipos as $tipo => $info)
                        @php
                            $count = $procesos->where('tipo_servicio', $tipo)->count();
                            if ($count > 0):
                        @endphp
                        <div class="flex items-center p-3 bg-gray-800 rounded-lg">
                            <div class="p-2 rounded-lg mr-3 {{ $info['color'] }}">
                                <i class="fas {{ $info['icon'] }} text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">{{ $info['text'] }}</p>
                                <p class="font-bold">{{ $count }}</p>
                            </div>
                        </div>
                        @php endif; @endphp
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lista de Procesos -->
        <div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden border border-gray-800">
            <div class="px-6 py-4 border-b border-gray-800">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-list-alt text-red-500 mr-2"></i>
                        Procesos Gestionados
                    </h2>
                    <div class="flex items-center space-x-4">
                        <p class="text-gray-400">
                            Mostrando {{ $procesos->count() }} proceso(s)
                        </p>
                        <select id="filtroTipo" onchange="filtrarPorTipo()" 
                                class="bg-gray-800 text-white border border-gray-700 rounded-lg px-3 py-1 text-sm">
                            <option value="">Todos los tipos</option>
                            @foreach($tipos as $tipo => $info)
                                @if($procesos->where('tipo_servicio', $tipo)->count() > 0)
                                    <option value="{{ $tipo }}">{{ $info['text'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            @if($procesos->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-700 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-400 mb-2">No hay procesos asignados</h3>
                    <p class="text-gray-500">No se encontraron procesos gestionados por este tramitador</p>
                    <a href="{{ route('landing.page') }}#consulta" 
                       class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i> Volver a consultar
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    ID / Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Tipo de Servicio
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Descripción
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Beneficiario
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Total / Pagos
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Detalles
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-800" id="tablaProcesos">
                            @foreach($procesos as $proceso)
                                @php
                                    $estadoBadge = match($proceso->estado_actual) {
                                        'finalizado' => 'estado-finalizado',
                                        'en_proceso' => 'estado-proceso',
                                        'enviado' => 'estado-enviado',
                                        default => 'estado-pendiente',
                                    };
                                    
                                    $estadoText = match($proceso->estado_actual) {
                                        'finalizado' => 'Finalizado',
                                        'en_proceso' => 'En Proceso',
                                        'enviado' => 'Enviado',
                                        default => 'Pendiente',
                                    };
                                    
                                    // Calcular pagos
                                    $totalPagado = $proceso->pagos->sum('valor');
                                    $saldo = $proceso->total_general - $totalPagado;
                                    
                                    // Obtener cédula del beneficiario
                                    $cedulaBeneficiario = $proceso->cedula_completa;
                                    
                                    // Tipo de servicio badge
                                    $tipoBadge = match($proceso->tipo_servicio) {
                                        'curso' => 'badge-curso',
                                        'renovacion' => 'badge-renovacion',
                                        'licencia' => 'badge-licencia',
                                        'controversia' => 'badge-controversia',
                                        'traspaso' => 'badge-traspaso',
                                        'runt' => 'badge-runt',
                                        default => '',
                                    };
                                    
                                    $tipoIcon = match($proceso->tipo_servicio) {
                                        'curso' => 'fa-graduation-cap',
                                        'renovacion' => 'fa-sync-alt',
                                        'licencia' => 'fa-id-card',
                                        'controversia' => 'fa-balance-scale',
                                        'traspaso' => 'fa-exchange-alt',
                                        'runt' => 'fa-car',
                                        default => 'fa-file-alt',
                                    };
                                    
                                    $tipoText = match($proceso->tipo_servicio) {
                                        'curso' => 'Curso',
                                        'renovacion' => 'Renovación',
                                        'licencia' => 'Licencia',
                                        'controversia' => 'Controversia',
                                        'traspaso' => 'Traspaso',
                                        'runt' => 'RUNT',
                                        default => ucfirst($proceso->tipo_servicio),
                                    };
                                @endphp
                                
                                <tr class="hover:bg-gray-800 transition duration-200" data-tipo="{{ $proceso->tipo_servicio }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium">
                                            #{{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }}
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            {{ $proceso->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tipoBadge }}">
                                                <i class="fas {{ $tipoIcon }} mr-1"></i>
                                                {{ $tipoText }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">
                                            {{ $proceso->descripcion_servicio }}
                                        </div>
                                        <div class="text-sm text-gray-400 mt-1">
                                            @if($proceso->cursos->count() > 0)
                                                <i class="fas fa-graduation-cap text-blue-500 mr-1"></i>
                                                {{ $proceso->cursos->count() }} curso(s)
                                            @elseif($proceso->renovaciones->count() > 0)
                                                <i class="fas fa-sync-alt text-green-500 mr-1"></i>
                                                {{ $proceso->renovaciones->count() }} renovación(es)
                                            @elseif($proceso->licencias->count() > 0)
                                                <i class="fas fa-id-card text-purple-500 mr-1"></i>
                                                {{ $proceso->licencias->count() }} licencia(s)
                                            @elseif($proceso->controversias->count() > 0)
                                                <i class="fas fa-balance-scale text-yellow-500 mr-1"></i>
                                                {{ $proceso->controversias->count() }} controversia(s)
                                            @elseif($proceso->traspasos->count() > 0)
                                                <i class="fas fa-exchange-alt text-blue-400 mr-1"></i>
                                                {{ $proceso->traspasos->count() }} traspaso(s)
                                            @elseif($proceso->runts->count() > 0)
                                                <i class="fas fa-car text-red-500 mr-1"></i>
                                                {{ $proceso->runts->count() }} RUNT(s)
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">
                                            <i class="fas fa-id-card mr-1 text-gray-400"></i>
                                            {{ $cedulaBeneficiario }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $proceso->nombre_beneficiario ?? 'Cliente' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoBadge }}">
                                            <i class="fas fa-circle text-xs mr-1"></i>
                                            {{ $estadoText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-lg">
                                            ${{ number_format($proceso->total_general, 0, ',', '.') }}
                                        </div>
                                        <div class="text-sm text-gray-400">
                                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                            Pagado: ${{ number_format($totalPagado, 0, ',', '.') }}
                                        </div>
                                        @if($saldo > 0)
                                        <div class="text-sm text-red-400 mt-1">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Saldo: ${{ number_format($saldo, 0, ',', '.') }}
                                        </div>
                                        @elseif($proceso->total_general > 0)
                                        <div class="text-sm text-green-400 mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Pagado completamente
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button onclick="mostrarDetalle({{ $proceso->id }})" 
                                                class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition duration-300 border border-gray-700">
                                            <i class="fas fa-eye mr-1"></i> Detalles
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Detalles del proceso (hidden por defecto) -->
                                <tr id="detalle-{{ $proceso->id }}" class="hidden bg-gray-800">
                                    <td colspan="7" class="px-6 py-4">
                                        <div class="bg-gray-900 rounded-lg p-6 border border-gray-800">
                                            <div class="flex justify-between items-center mb-4">
                                                <h4 class="font-bold text-xl text-white">
                                                    <i class="fas fa-info-circle text-red-500 mr-2"></i>
                                                    Detalles del Proceso #{{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }}
                                                </h4>
                                                <button onclick="mostrarDetalle({{ $proceso->id }})" 
                                                        class="text-gray-400 hover:text-white">
                                                    <i class="fas fa-times text-xl"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Información general -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                                <div class="bg-gray-800 p-4 rounded-lg">
                                                    <h5 class="font-semibold text-gray-300 mb-2">
                                                        <i class="fas fa-info text-red-500 mr-2"></i> Información General
                                                    </h5>
                                                    <p class="text-sm text-gray-400">Creado: {{ $proceso->created_at->format('d/m/Y H:i') }}</p>
                                                    <p class="text-sm text-gray-400">Actualizado: {{ $proceso->updated_at->format('d/m/Y H:i') }}</p>
                                                    <p class="text-sm text-gray-400">Tipo de Servicio: 
                                                        <span class="{{ $tipoBadge }} px-2 py-1 rounded text-xs">
                                                            {{ $tipoText }}
                                                        </span>
                                                    </p>
                                                </div>
                                                
                                                <div class="bg-gray-800 p-4 rounded-lg">
                                                    <h5 class="font-semibold text-gray-300 mb-2">
                                                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i> Estado Financiero
                                                    </h5>
                                                    <div class="flex justify-between">
                                                        <div>
                                                            <p class="text-sm text-gray-400">Total</p>
                                                            <p class="font-semibold">${{ number_format($proceso->total_general, 0, ',', '.') }}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm text-gray-400">Pagado</p>
                                                            <p class="font-semibold text-green-500">${{ number_format($totalPagado, 0, ',', '.') }}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-sm text-gray-400">Saldo</p>
                                                            <p class="font-semibold {{ $saldo > 0 ? 'text-red-400' : 'text-green-500' }}">
                                                                ${{ number_format($saldo, 0, ',', '.') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Servicios específicos -->
                                            @if($proceso->cursos->count() > 0)
                                                <div class="mb-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-graduation-cap text-red-500 mr-2"></i> Cursos:
                                                    </h5>
                                                    <div class="space-y-3">
                                                        @foreach($proceso->cursos as $curso)
                                                            <div class="bg-gray-800 p-4 rounded-lg border-l-4 border-red-500">
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        <p class="font-medium">{{ $curso->curso->categoria ?? 'Curso' }}</p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-id-card mr-1"></i> Cédula: {{ $curso->cedula }}
                                                                        </p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-percentage mr-1"></i> Porcentaje: {{ $curso->porcentaje }}%
                                                                        </p>
                                                                    </div>
                                                                    <div>
                                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                                            {{ $curso->estado == 'finalizado' ? 'estado-finalizado' : 
                                                                               ($curso->estado == 'en_proceso' ? 'estado-proceso' : 
                                                                               'estado-pendiente') }}">
                                                                            {{ ucfirst($curso->estado) }}
                                                                        </span>
                                                                        <div class="mt-2 text-right">
                                                                            <p class="text-sm text-green-400">
                                                                                Valor: ${{ number_format($curso->valor_recibir, 0, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($proceso->renovaciones->count() > 0)
                                                <div class="mb-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-sync-alt text-green-500 mr-2"></i> Renovaciones:
                                                    </h5>
                                                    <div class="space-y-3">
                                                        @foreach($proceso->renovaciones as $renovacion)
                                                            <div class="bg-gray-800 p-4 rounded-lg border-l-4 border-green-500">
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        <p class="font-medium">{{ $renovacion->renovacion->nombre ?? 'Renovación' }}</p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-id-card mr-1"></i> Cédula: {{ $renovacion->cedula }}
                                                                        </p>
                                                                        <div class="flex space-x-4 mt-1">
                                                                            <span class="text-xs px-2 py-1 rounded {{ $renovacion->incluye_examen ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-400' }}">
                                                                                Examen: {{ $renovacion->incluye_examen ? 'Sí' : 'No' }}
                                                                            </span>
                                                                            <span class="text-xs px-2 py-1 rounded {{ $renovacion->incluye_lamina ? 'bg-green-900/30 text-green-400' : 'bg-gray-700 text-gray-400' }}">
                                                                                Lámina: {{ $renovacion->incluye_lamina ? 'Sí' : 'No' }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                                            {{ $renovacion->estado == 'finalizado' ? 'estado-finalizado' : 
                                                                               ($renovacion->estado == 'en_proceso' ? 'estado-proceso' : 
                                                                               'estado-pendiente') }}">
                                                                            {{ ucfirst($renovacion->estado) }}
                                                                        </span>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-green-400">
                                                                                Valor: ${{ number_format($renovacion->valor_total, 0, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($proceso->licencias->count() > 0)
                                                <div class="mb-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-id-card text-purple-500 mr-2"></i> Licencias:
                                                    </h5>
                                                    <div class="space-y-3">
                                                        @foreach($proceso->licencias as $licencia)
                                                            <div class="bg-gray-800 p-4 rounded-lg border-l-4 border-purple-500">
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        <p class="font-medium">Cédula: {{ $licencia->cedula }}</p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-university mr-1"></i> Escuela: {{ $licencia->escuela->nombre ?? 'N/A' }}
                                                                        </p>
                                                                        @if(!empty($licencia->categorias_seleccionadas))
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-tags mr-1"></i> Categorías: 
                                                                            @if(is_array($licencia->categorias_seleccionadas))
                                                                                {{ implode(', ', $licencia->categorias_seleccionadas) }}
                                                                            @else
                                                                                {{ $licencia->categorias_seleccionadas }}
                                                                            @endif
                                                                        </p>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                                            {{ $licencia->estado == 'finalizado' ? 'estado-finalizado' : 
                                                                               ($licencia->estado == 'en_proceso' ? 'estado-proceso' : 
                                                                               'estado-pendiente') }}">
                                                                            {{ ucfirst($licencia->estado) }}
                                                                        </span>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-green-400">
                                                                                Valor: ${{ number_format($licencia->valor_total_licencia, 0, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($proceso->controversias->count() > 0)
                                                <div class="mb-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-balance-scale text-yellow-500 mr-2"></i> Controversias:
                                                    </h5>
                                                    <div class="space-y-3">
                                                        @foreach($proceso->controversias as $controversia)
                                                            <div class="bg-gray-800 p-4 rounded-lg border-l-4 border-yellow-500">
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        <p class="font-medium">{{ $controversia->categoriaControversia->nombre ?? 'Controversia' }}</p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-id-card mr-1"></i> Cédula: {{ $controversia->cedula }}
                                                                        </p>
                                                                        @if($controversia->codigo_controversia)
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-hashtag mr-1"></i> Código: {{ $controversia->codigo_controversia }}
                                                                        </p>
                                                                        @endif
                                                                        @if($controversia->fecha_hora_cita)
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-calendar-alt mr-1"></i> Cita: {{ $controversia->fecha_hora_cita->format('d/m/Y H:i') }}
                                                                        </p>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                                            {{ $controversia->estado == 'finalizado' ? 'estado-finalizado' : 
                                                                               ($controversia->estado == 'en_proceso' ? 'estado-proceso' : 
                                                                               'estado-pendiente') }}">
                                                                            {{ ucfirst($controversia->estado) }}
                                                                        </span>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-green-400">
                                                                                Valor: ${{ number_format($controversia->valor_controversia, 0, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($proceso->traspasos->count() > 0)
                                                <div class="mb-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-exchange-alt text-blue-400 mr-2"></i> Traspasos:
                                                    </h5>
                                                    <div class="space-y-3">
                                                        @foreach($proceso->traspasos as $traspaso)
                                                            <div class="bg-gray-800 p-4 rounded-lg border-l-4 border-blue-400">
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        <p class="font-medium">{{ $traspaso->nombre_propietario }} → {{ $traspaso->nombre_comprador }}</p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-id-card mr-1"></i> Propietario: {{ $traspaso->cedula }}
                                                                        </p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-id-card mr-1"></i> Comprador: {{ $traspaso->cedula_comprador }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                                            {{ $traspaso->estado == 'finalizado' ? 'estado-finalizado' : 
                                                                               ($traspaso->estado == 'en_proceso' ? 'estado-proceso' : 
                                                                               'estado-pendiente') }}">
                                                                            {{ ucfirst($traspaso->estado) }}
                                                                        </span>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-green-400">
                                                                                Valor: ${{ number_format($traspaso->total_recibir, 0, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($proceso->runts->count() > 0)
                                                <div class="mb-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-car text-red-400 mr-2"></i> RUNT:
                                                    </h5>
                                                    <div class="space-y-3">
                                                        @foreach($proceso->runts as $runt)
                                                            <div class="bg-gray-800 p-4 rounded-lg border-l-4 border-red-400">
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        <p class="font-medium">{{ $runt->nombre }}</p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-id-card mr-1"></i> Cédula: {{ $runt->cedula }}
                                                                        </p>
                                                                        <p class="text-sm text-gray-400">
                                                                            <i class="fas fa-hashtag mr-1"></i> Número: {{ $runt->numero }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="text-right">
                                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                                            {{ $runt->estado == 'finalizado' ? 'estado-finalizado' : 
                                                                               ($runt->estado == 'en_proceso' ? 'estado-proceso' : 
                                                                               'estado-pendiente') }}">
                                                                            {{ ucfirst($runt->estado) }}
                                                                        </span>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-green-400">
                                                                                Valor: ${{ number_format($runt->valor_recibir, 0, ',', '.') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- Historial de pagos -->
                                            @if($proceso->pagos->count() > 0)
                                                <div class="mt-6">
                                                    <h5 class="font-semibold text-gray-300 mb-3">
                                                        <i class="fas fa-receipt text-purple-500 mr-2"></i> Historial de Pagos:
                                                    </h5>
                                                    <div class="bg-gray-800 rounded-lg overflow-hidden">
                                                        <table class="min-w-full divide-y divide-gray-700">
                                                            <thead class="bg-gray-900">
                                                                <tr>
                                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Fecha</th>
                                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Método</th>
                                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Valor</th>
                                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Referencia</th>
                                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400">Estado</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="divide-y divide-gray-700">
                                                                @foreach($proceso->pagos as $pago)
                                                                    <tr class="hover:bg-gray-700">
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                                                            {{ $pago->fecha_pago->format('d/m/Y') }}
                                                                        </td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                                                            <span class="capitalize">{{ $pago->metodo }}</span>
                                                                        </td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-green-500">
                                                                            ${{ number_format($pago->valor, 0, ',', '.') }}
                                                                        </td>
                                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400">
                                                                            {{ $pago->referencia ?? '-' }}
                                                                        </td>
                                                                        <td class="px-4 py-3 whitespace-nowrap">
                                                                            <span class="px-2 py-1 text-xs rounded-full bg-green-900/30 text-green-400">
                                                                                Confirmado
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot class="bg-gray-900">
                                                                <tr>
                                                                    <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-300">Total Pagado</td>
                                                                    <td class="px-4 py-3 text-sm font-bold text-green-500">
                                                                        ${{ number_format($totalPagado, 0, ',', '.') }}
                                                                    </td>
                                                                    <td colspan="2"></td>
                                                                </tr>
                                                            </tfoot>
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
                
                <!-- Pie de tabla -->
                <div class="px-6 py-4 border-t border-gray-800 bg-gray-900">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            <i class="fas fa-info-circle mr-2"></i>
                            Haz clic en "Detalles" para ver más información de cada proceso
                        </div>
                        <div class="text-sm text-gray-400">
                            Mostrando {{ $procesos->count() }} de {{ $procesos->count() }} procesos
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Resumen financiero -->
        <div class="mt-8 bg-gray-900 rounded-xl shadow-lg p-6 border border-gray-800">
            <h3 class="text-xl font-bold mb-4">
                <i class="fas fa-chart-bar text-red-500 mr-2"></i> Resumen Financiero
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-300 mb-2">Ingresos Totales</h4>
                    <p class="text-2xl font-bold text-green-400">
                        ${{ number_format($procesos->sum('total_general'), 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">Valor total de todos los procesos</p>
                </div>
                
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-300 mb-2">Total Pagado</h4>
                    <p class="text-2xl font-bold text-blue-400">
                        ${{ number_format($procesos->sum(fn($p) => $p->pagos->sum('valor')), 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">Suma de todos los pagos recibidos</p>
                </div>
                
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-300 mb-2">Saldo Pendiente</h4>
                    <p class="text-2xl font-bold {{ $procesos->sum('total_general') - $procesos->sum(fn($p) => $p->pagos->sum('valor')) > 0 ? 'text-red-400' : 'text-green-400' }}">
                        ${{ number_format($procesos->sum('total_general') - $procesos->sum(fn($p) => $p->pagos->sum('valor')), 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">Saldo total por cobrar</p>
                </div>
            </div>
        </div>
        
        <!-- Pie de página -->
        <footer class="mt-8 pt-6 border-t border-gray-800 text-center text-gray-500 text-sm">
            <p>
                <i class="fas fa-shield-alt mr-2"></i>
                Ruler Soluciones de Tránsito &copy; {{ date('Y') }} - Portal de Tramitadores
            </p>
            <p class="mt-2">
                <i class="fas fa-user-tie mr-1"></i>
                Tramitador: {{ $tramitador->nombre }} | Cédula: {{ $tramitador->cedula }}
            </p>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        function mostrarDetalle(procesoId) {
            const detalle = document.getElementById(`detalle-${procesoId}`);
            detalle.classList.toggle('hidden');
            
            const boton = event.target.closest('button');
            if (detalle.classList.contains('hidden')) {
                boton.innerHTML = '<i class="fas fa-eye mr-1"></i> Detalles';
            } else {
                boton.innerHTML = '<i class="fas fa-eye-slash mr-1"></i> Ocultar';
            }
        }
        
        // Función para imprimir
        function imprimirResumen() {
            window.print();
        }
        
        // Función para filtrar por tipo de servicio
        function filtrarPorTipo() {
            const filtro = document.getElementById('filtroTipo').value;
            const filas = document.querySelectorAll('#tablaProcesos tr[data-tipo]');
            
            filas.forEach(fila => {
                if (filtro === '' || fila.dataset.tipo === filtro) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }
        
        // Añadir efecto hover a las filas de la tabla
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('hidden')) {
                        this.style.backgroundColor = '#1f2937';
                    }
                });
                row.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('hidden') && !this.id.includes('detalle-')) {
                        this.style.backgroundColor = '';
                    }
                });
            });
            
            // Contador de procesos visibles
            function actualizarContador() {
                const filtro = document.getElementById('filtroTipo').value;
                const filas = document.querySelectorAll('#tablaProcesos tr[data-tipo]');
                let visibleCount = 0;
                
                filas.forEach(fila => {
                    if (filtro === '' || fila.dataset.tipo === filtro) {
                        if (fila.style.display !== 'none') {
                            visibleCount++;
                        }
                    }
                });
                
                // Actualizar texto
                const contadorElement = document.querySelector('.text-gray-400:contains("Mostrando")');
                if (contadorElement) {
                    contadorElement.textContent = `Mostrando ${visibleCount} de {{ $procesos->count() }} procesos`;
                }
            }
            
            // Actualizar contador cuando cambia el filtro
            document.getElementById('filtroTipo').addEventListener('change', actualizarContador);
        });
    </script>
</body>
</html>