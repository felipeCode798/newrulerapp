<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Proceso #{{ $proceso->id }}</title>
    <style>
        @page {
            margin: 50px 25px;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2C3E50;
        }
        
        .company-info {
            flex: 2;
        }
        
        .invoice-info {
            flex: 1;
            text-align: right;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2C3E50;
            margin: 0;
        }
        
        .company-details {
            color: #666;
            margin-top: 10px;
        }
        
        .invoice-title {
            font-size: 24px;
            color: #2C3E50;
            margin: 0;
        }
        
        .invoice-number {
            font-size: 20px;
            color: #666;
            margin: 5px 0;
        }
        
        .client-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .client-header {
            font-size: 16px;
            color: #2C3E50;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .section-title {
            font-size: 18px;
            color: #2C3E50;
            margin: 25px 0 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2C3E50;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .detail-table th {
            background-color: #2C3E50;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .detail-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        .detail-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .amount {
            text-align: right;
        }
        
        .subtotal-row {
            font-weight: bold;
            background-color: #e8f5e8 !important;
        }
        
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #2C3E50;
        }
        
        .footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .badge-cliente {
            background-color: #28a745;
            color: white;
        }
        
        .badge-tramitador {
            background-color: #17a2b8;
            color: white;
        }
        
        .qr-code {
            text-align: center;
            margin: 30px 0;
        }
        
        .terms {
            margin-top: 40px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="company-info">
            <h1 class="company-name">SISTEMA JURÍDICO DE TRÁNSITO</h1>
            <div class="company-details">
                <div>Nit: 900.000.000-1</div>
                <div>Dirección: Calle 123 #45-67, Ciudad</div>
                <div>Teléfono: (601) 123-4567</div>
                <div>Email: info@sistema-transito.com</div>
            </div>
        </div>
        <div class="invoice-info">
            <h2 class="invoice-title">FACTURA</h2>
            <div class="invoice-number">No. {{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div>Fecha: {{ $proceso->created_at->format('d/m/Y') }}</div>
            <div>Hora: {{ $proceso->created_at->format('H:i:s') }}</div>
        </div>
    </div>

    <div class="client-info">
        <div class="client-header">
            @if($proceso->tipo_usuario == 'cliente')
                INFORMACIÓN DEL CLIENTE
            @else
                INFORMACIÓN DEL TRAMITADOR
            @endif
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nombre:</span>
                @if($proceso->tipo_usuario == 'cliente')
                    {{ $proceso->cliente->nombre ?? 'No especificado' }}
                @else
                    {{ $proceso->tramitador->nombre ?? 'No especificado' }}
                @endif
            </div>
            <div class="info-item">
                <span class="info-label">Cédula:</span>
                @if($proceso->tipo_usuario == 'cliente')
                    {{ $proceso->cliente->cedula ?? 'No especificado' }}
                @else
                    {{ $proceso->tramitador->cedula ?? 'No especificado' }}
                @endif
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                @if($proceso->tipo_usuario == 'cliente')
                    {{ $proceso->cliente->email ?? 'No especificado' }}
                @else
                    {{ $proceso->tramitador->email ?? 'No especificado' }}
                @endif
            </div>
            <div class="info-item">
                <span class="info-label">Teléfono:</span>
                @if($proceso->tipo_usuario == 'cliente')
                    {{ $proceso->cliente->telefono ?? 'No especificado' }}
                @else
                    {{ $proceso->tramitador->telefono ?? 'No especificado' }}
                @endif
            </div>
            <div class="info-item">
                <span class="info-label">Tipo:</span>
                @if($proceso->tipo_usuario == 'cliente')
                    <span class="badge badge-cliente">Cliente</span>
                @else
                    <span class="badge badge-tramitador">Tramitador</span>
                @endif
            </div>
        </div>
    </div>

    @if($proceso->cursos()->count() > 0)
    <h3 class="section-title">CURSOS</h3>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Curso</th>
                <th>Cédula</th>
                <th>Porcentaje</th>
                <th>Valor Tránsito</th>
                <th>Valor a Recibir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proceso->cursos as $curso)
            <tr>
                <td>{{ $curso->curso->categoria ?? 'N/A' }}</td>
                <td>{{ $curso->cedula }}</td>
                <td>{{ $curso->porcentaje }}%</td>
                <td class="amount">$ {{ number_format($curso->valor_transito, 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($curso->valor_recibir, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="4" class="amount"><strong>Subtotal Cursos:</strong></td>
                <td class="amount"><strong>$ {{ number_format($totalCursos, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    @endif

    @if($proceso->renovaciones()->count() > 0)
    <h3 class="section-title">RENOVACIONES</h3>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Renovación</th>
                <th>Cédula</th>
                <th>Examen</th>
                <th>Lámina</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proceso->renovaciones as $renovacion)
            <tr>
                <td>{{ $renovacion->renovacion->nombre ?? 'N/A' }}</td>
                <td>{{ $renovacion->cedula }}</td>
                <td>{{ $renovacion->incluye_examen ? 'Sí' : 'No' }}</td>
                <td>{{ $renovacion->incluye_lamina ? 'Sí' : 'No' }}</td>
                <td class="amount">$ {{ number_format($renovacion->valor_total, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="4" class="amount"><strong>Subtotal Renovaciones:</strong></td>
                <td class="amount"><strong>$ {{ number_format($totalRenovaciones, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    @endif

    @if($proceso->licencias()->count() > 0)
    <h3 class="section-title">LICENCIAS</h3>
    <table class="detail-table">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Categorías</th>
                <th>Escuela</th>
                <th>Carta Escuela</th>
                <th>Examen Médico</th>
                <th>Impresión</th>
                <th>Sin Curso</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proceso->licencias as $licencia)
            <tr>
                <td>{{ $licencia->cedula }}</td>
                <td>
                    @if(is_array($licencia->categorias_seleccionadas))
                        @php
                            $categorias = \App\Models\CategoriaLicencia::whereIn('id', $licencia->categorias_seleccionadas)->pluck('nombre')->toArray();
                        @endphp
                        {{ implode(', ', $categorias) }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $licencia->escuela->nombre ?? 'N/A' }}</td>
                <td class="amount">$ {{ number_format($licencia->valor_carta_escuela, 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($licencia->valor_examen_medico, 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($licencia->valor_impresion, 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($licencia->valor_sin_curso, 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($licencia->valor_total_licencia, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="subtotal-row">
                <td colspan="7" class="amount"><strong>Subtotal Licencias:</strong></td>
                <td class="amount"><strong>$ {{ number_format($totalLicencias, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- Repite secciones similares para Traspasos, RUNT y Controversias -->

    <div class="total-section">
        <div class="total-amount">
            TOTAL GENERAL: $ {{ number_format($proceso->total_general, 2, ',', '.') }}
        </div>
        <div style="margin-top: 10px; color: #666;">
            Pesos Colombianos
        </div>
    </div>

    <div class="terms">
        <p><strong>TÉRMINOS Y CONDICIONES:</strong></p>
        <p>1. Esta factura es un documento legal que respalda la transacción.</p>
        <p>2. Los valores están expresados en pesos colombianos (COP).</p>
        <p>3. Para reclamaciones, presentar esta factura dentro de los 30 días siguientes.</p>
        <p>4. El pago debe realizarse dentro de los 15 días siguientes a la emisión.</p>
    </div>

    <div class="footer">
        <p>Sistema Jurídico de Tránsito - Factura No. {{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }} - Página 1 de 1</p>
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>