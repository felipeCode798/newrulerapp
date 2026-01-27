<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Procesos - Sistema Jurídico de Tránsito</title>
    <style>
        @page {
            margin: 50px 25px;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2C3E50;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #2C3E50;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .table th {
            background-color: #2C3E50;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .table tr:hover {
            background-color: #f5f5f5;
        }
        
        .total-row {
            background-color: #e8f5e8 !important;
            font-weight: bold;
        }
        
        .amount {
            text-align: right;
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
        
        .summary {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #2C3E50;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEMA JURÍDICO DE TRÁNSITO</h1>
        <p>Reporte de Procesos</p>
        <p>Fecha de exportación: {{ $fechaExportacion }}</p>
        <p>Total de registros: {{ $procesos->count() }}</p>
    </div>

    <div class="info-section">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Procesos Clientes</div>
                <div class="summary-value">{{ $procesos->where('tipo_usuario', 'cliente')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Procesos Tramitadores</div>
                <div class="summary-value">{{ $procesos->where('tipo_usuario', 'tramitador')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total General</div>
                <div class="summary-value">$ {{ number_format($totalGeneral, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Cliente/Tramitador</th>
                <th>Cédula</th>
                <th>Cursos</th>
                <th>Renovaciones</th>
                <th>Licencias</th>
                <th>Traspasos</th>
                <th>RUNT</th>
                <th>Controversias</th>
                <th>Total</th>
                <th>Creado Por</th>
            </tr>
        </thead>
        <tbody>
            @foreach($procesos as $index => $proceso)
            <tr>
                <td>{{ $proceso->id }}</td>
                <td>{{ $proceso->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($proceso->tipo_usuario == 'cliente')
                        <span class="badge badge-cliente">Cliente</span>
                    @else
                        <span class="badge badge-tramitador">Tramitador</span>
                    @endif
                </td>
                <td>
                    @if($proceso->tipo_usuario == 'cliente')
                        {{ $proceso->cliente->nombre ?? '-' }}
                    @else
                        {{ $proceso->tramitador->nombre ?? '-' }}
                    @endif
                </td>
                <td>
                    @if($proceso->tipo_usuario == 'cliente')
                        {{ $proceso->cliente->cedula ?? '-' }}
                    @else
                        {{ $proceso->tramitador->cedula ?? '-' }}
                    @endif
                </td>
                <td class="amount">$ {{ number_format($proceso->cursos()->sum('valor_recibir'), 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($proceso->renovaciones()->sum('valor_total'), 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($proceso->licencias()->sum('valor_total_licencia'), 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($proceso->traspasos()->sum('total_recibir'), 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($proceso->runts()->sum('valor_recibir'), 2, ',', '.') }}</td>
                <td class="amount">$ {{ number_format($proceso->controversias()->sum('valor_controversia'), 2, ',', '.') }}</td>
                <td class="amount"><strong>$ {{ number_format($proceso->total_general, 2, ',', '.') }}</strong></td>
                <td>{{ $proceso->createdBy->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5"><strong>TOTAL GENERAL</strong></td>
                <td class="amount"><strong>$ {{ number_format($procesos->sum(fn($p) => $p->cursos()->sum('valor_recibir')), 2, ',', '.') }}</strong></td>
                <td class="amount"><strong>$ {{ number_format($procesos->sum(fn($p) => $p->renovaciones()->sum('valor_total')), 2, ',', '.') }}</strong></td>
                <td class="amount"><strong>$ {{ number_format($procesos->sum(fn($p) => $p->licencias()->sum('valor_total_licencia')), 2, ',', '.') }}</strong></td>
                <td class="amount"><strong>$ {{ number_format($procesos->sum(fn($p) => $p->traspasos()->sum('total_recibir')), 2, ',', '.') }}</strong></td>
                <td class="amount"><strong>$ {{ number_format($procesos->sum(fn($p) => $p->runts()->sum('valor_recibir')), 2, ',', '.') }}</strong></td>
                <td class="amount"><strong>$ {{ number_format($procesos->sum(fn($p) => $p->controversias()->sum('valor_controversia')), 2, ',', '.') }}</strong></td>
                <td class="amount"><strong>$ {{ number_format($totalGeneral, 2, ',', '.') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Sistema Jurídico de Tránsito - Página {{ $pdf->getPage() }} de {{ $pdf->getPageCount() }}</p>
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>