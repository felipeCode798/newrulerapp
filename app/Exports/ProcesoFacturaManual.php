<?php

namespace App\Exports;

use App\Models\Proceso;

class ProcesoFacturaManual
{
    protected $procesos;

    public function __construct($procesos)
    {
        $this->procesos = $procesos;
    }

    public function output()
    {
        // Si es un solo proceso, mostrar factura individual
        if ($this->procesos instanceof \Illuminate\Database\Eloquent\Collection && $this->procesos->count() === 1) {
            $proceso = $this->procesos->first();
            $html = $this->generateSingleInvoice($proceso);
        } else {
            // Si son múltiples procesos, mostrar reporte consolidado
            $html = $this->generateConsolidatedReport($this->procesos);
        }

        // Guardar el HTML en un archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'pdf_');
        file_put_contents($tempFile, $html);

        // Usar el navegador para convertir a PDF (alternativa manual)
        // En un entorno real, usarías wkhtmltopdf o similar
        // Por ahora, devolvemos el HTML y el usuario puede imprimir como PDF
        
        return $this->generateHtmlResponse($html);
    }

    public function download($filename)
    {
        $html = $this->output();
        
        // Para descargar como archivo HTML que se puede imprimir como PDF
        return response()->streamDownload(
            function () use ($html) {
                echo $html;
            },
            $filename . '.html',
            [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.html"',
            ]
        );
    }

    private function generateHtmlResponse($html)
    {
        return $html;
    }

    private function generateSingleInvoice(Proceso $proceso)
    {
        $totalCursos = $proceso->cursos()->sum('valor_recibir');
        $totalRenovaciones = $proceso->renovaciones()->sum('valor_total');
        $totalLicencias = $proceso->licencias()->sum('valor_total_licencia');
        $totalTraspasos = $proceso->traspasos()->sum('total_recibir');
        $totalRunts = $proceso->runts()->sum('valor_recibir');
        $totalControversias = $proceso->controversias()->sum('valor_controversia');

        // Usar el método getCedulaCompletaAttribute del modelo para obtener la cédula correcta
        $clienteNombre = $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->nombre ?? '-' : '-';
        $clienteCedula = $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->cedula ?? '-' : '-';
        $tramitadorNombre = $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->nombre ?? '-' : '-';
        $tramitadorCedula = $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->cedula ?? '-' : '-';
        $cedulaBeneficiario = $proceso->cedula_completa; // Usar el método corregido del modelo

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Proceso #' . $proceso->id . '</title>
    <style>
        @media print {
            @page {
                margin: 20mm 15mm;
            }
            
            body {
                margin: 0;
                padding: 0;
                font-size: 12pt;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-instructions {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 20px;
            padding: 0;
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
            font-size: 24px;
            font-weight: bold;
            color: #2C3E50;
            margin: 0;
        }
        
        .invoice-title {
            font-size: 22px;
            color: #2C3E50;
            margin: 0;
        }
        
        .invoice-number {
            font-size: 18px;
            color: #666;
            margin: 5px 0;
        }
        
        .client-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .client-header {
            font-size: 14px;
            color: #2C3E50;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        
        .detail-table th {
            background-color: #2C3E50;
            color: white;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .detail-table td {
            padding: 6px 8px;
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
            font-size: 20px;
            font-weight: bold;
            color: #2C3E50;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #666;
            font-size: 10px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
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
        
        .print-instructions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .print-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 0;
            cursor: pointer;
            border: none;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="print-instructions no-print">
        <strong>Instrucciones para guardar como PDF:</strong><br>
        1. Haz clic en el botón "Imprimir"<br>
        2. En el diálogo de impresión, selecciona "Guardar como PDF" como impresora<br>
        3. Configura el tamaño de página a A4 y márgenes a 20mm<br>
        4. Haz clic en "Guardar"<br>
        <button class="print-btn" onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>

    <div class="invoice-header">
        <div class="company-info">
            <h1 class="company-name">SISTEMA JURÍDICO DE TRÁNSITO</h1>
            <div style="color: #666;">
                <div>Nit: 900.000.000-1</div>
                <div>Dirección: Calle 123 #45-67, Ciudad</div>
                <div>Teléfono: (601) 123-4567</div>
                <div>Email: info@sistema-transito.com</div>
            </div>
        </div>
        <div class="invoice-info">
            <h2 class="invoice-title">FACTURA</h2>
            <div class="invoice-number">No. ' . str_pad($proceso->id, 6, '0', STR_PAD_LEFT) . '</div>
            <div>Fecha: ' . $proceso->created_at->format('d/m/Y') . '</div>
            <div>Hora: ' . $proceso->created_at->format('H:i:s') . '</div>
        </div>
    </div>

    <div class="client-info">
        <div class="client-header">';
        
        if ($proceso->tipo_usuario == 'cliente') {
            $html .= 'INFORMACIÓN DEL CLIENTE';
        } else {
            $html .= 'INFORMACIÓN DEL TRAMITADOR Y BENEFICIARIO';
        }
        
        $html .= '</div>
        <div class="info-grid">
            <div class="info-item">
                <strong>' . ($proceso->tipo_usuario == 'cliente' ? 'Nombre Cliente:' : 'Nombre Tramitador:') . '</strong> ';
        
        if ($proceso->tipo_usuario == 'cliente') {
            $html .= $clienteNombre;
        } else {
            $html .= $tramitadorNombre;
        }
        
        $html .= '</div>
            <div class="info-item">
                <strong>' . ($proceso->tipo_usuario == 'cliente' ? 'Cédula Cliente:' : 'Cédula Tramitador:') . '</strong> ';
        
        if ($proceso->tipo_usuario == 'cliente') {
            $html .= $clienteCedula;
        } else {
            $html .= $tramitadorCedula;
        }
        
        // Mostrar información del beneficiario cuando es tramitador
        if ($proceso->tipo_usuario == 'tramitador') {
            $html .= '</div>
            <div class="info-item">
                <strong>Cédula Beneficiario:</strong> ' . $cedulaBeneficiario . '
            </div>';
        } else {
            $html .= '</div>
            <div class="info-item"></div>';
        }
        
        $html .= '
            <div class="info-item">
                <strong>Tipo:</strong> ';
        
        if ($proceso->tipo_usuario == 'cliente') {
            $html .= '<span class="badge badge-cliente">Cliente</span>';
        } else {
            $html .= '<span class="badge badge-tramitador">Tramitador</span>';
        }
        
        $html .= '</div>
        </div>
    </div>';

        // Sección de Cursos
        if ($proceso->cursos()->count() > 0) {
            $html .= '<h3 style="font-size: 18px; color: #2C3E50; margin: 25px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #2C3E50;">CURSOS</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Cédula Beneficiario</th>
                        <th>Porcentaje</th>
                        <th>Valor a Recibir</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($proceso->cursos as $curso) {
                $html .= '<tr>
                    <td>' . ($curso->curso->categoria ?? 'N/A') . '</td>
                    <td>' . $curso->cedula . '</td>
                    <td>' . $curso->porcentaje . '%</td>
                    <td class="amount">$ ' . number_format($curso->valor_recibir, 2, ',', '.') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="3" class="amount"><strong>Subtotal Cursos:</strong></td>
                        <td class="amount"><strong>$ ' . number_format($totalCursos, 2, ',', '.') . '</strong></td>
                    </tr>
                </tfoot>
            </table>';
        }

        // Sección de Renovaciones
        if ($proceso->renovaciones()->count() > 0) {
            $html .= '<h3 style="font-size: 18px; color: #2C3E50; margin: 25px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #2C3E50;">RENOVACIONES</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Renovación</th>
                        <th>Cédula Beneficiario</th>
                        <th>Examen</th>
                        <th>Lámina</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($proceso->renovaciones as $renovacion) {
                $html .= '<tr>
                    <td>' . ($renovacion->renovacion->nombre ?? 'N/A') . '</td>
                    <td>' . $renovacion->cedula . '</td>
                    <td>' . ($renovacion->incluye_examen ? 'Sí' : 'No') . '</td>
                    <td>' . ($renovacion->incluye_lamina ? 'Sí' : 'No') . '</td>
                    <td class="amount">$ ' . number_format($renovacion->valor_total, 2, ',', '.') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="4" class="amount"><strong>Subtotal Renovaciones:</strong></td>
                        <td class="amount"><strong>$ ' . number_format($totalRenovaciones, 2, ',', '.') . '</strong></td>
                    </tr>
                </tfoot>
            </table>';
        }

        // Sección de Licencias
        if ($proceso->licencias()->count() > 0) {
            $html .= '<h3 style="font-size: 18px; color: #2C3E50; margin: 25px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #2C3E50;">LICENCIAS</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Cédula Beneficiario</th>
                        <th>Categorías</th>
                        <th>Escuela</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($proceso->licencias as $licencia) {
                $categorias = '-';
                if (is_array($licencia->categorias_seleccionadas)) {
                    $categoriasArray = \App\Models\CategoriaLicencia::whereIn('id', $licencia->categorias_seleccionadas)->pluck('nombre')->toArray();
                    $categorias = implode(', ', $categoriasArray);
                }
                
                $html .= '<tr>
                    <td>' . $licencia->cedula . '</td>
                    <td>' . $categorias . '</td>
                    <td>' . ($licencia->escuela->nombre ?? 'N/A') . '</td>
                    <td class="amount">$ ' . number_format($licencia->valor_total_licencia, 2, ',', '.') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="3" class="amount"><strong>Subtotal Licencias:</strong></td>
                        <td class="amount"><strong>$ ' . number_format($totalLicencias, 2, ',', '.') . '</strong></td>
                    </tr>
                </tfoot>
            </table>';
        }

        // Sección de Traspasos
        if ($proceso->traspasos()->count() > 0) {
            $html .= '<h3 style="font-size: 18px; color: #2C3E50; margin: 25px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #2C3E50;">TRASPASOS</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Cédula Propietario</th>
                        <th>Propietario</th>
                        <th>Comprador</th>
                        <th>Cédula Comprador</th>
                        <th>Total a Recibir</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($proceso->traspasos as $traspaso) {
                $html .= '<tr>
                    <td>' . $traspaso->cedula . '</td>
                    <td>' . $traspaso->nombre_propietario . '</td>
                    <td>' . $traspaso->nombre_comprador . '</td>
                    <td>' . $traspaso->cedula_comprador . '</td>
                    <td class="amount">$ ' . number_format($traspaso->total_recibir, 2, ',', '.') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="4" class="amount"><strong>Subtotal Traspasos:</strong></td>
                        <td class="amount"><strong>$ ' . number_format($totalTraspasos, 2, ',', '.') . '</strong></td>
                    </tr>
                </tfoot>
            </table>';
        }

        // Sección de RUNT
        if ($proceso->runts()->count() > 0) {
            $html .= '<h3 style="font-size: 18px; color: #2C3E50; margin: 25px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #2C3E50;">RUNT</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cédula Beneficiario</th>
                        <th>Número</th>
                        <th>Total a Recibir</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($proceso->runts as $runt) {
                $html .= '<tr>
                    <td>' . $runt->nombre . '</td>
                    <td>' . $runt->cedula . '</td>
                    <td>' . $runt->numero . '</td>
                    <td class="amount">$ ' . number_format($runt->valor_recibir, 2, ',', '.') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="3" class="amount"><strong>Subtotal RUNT:</strong></td>
                        <td class="amount"><strong>$ ' . number_format($totalRunts, 2, ',', '.') . '</strong></td>
                    </tr>
                </tfoot>
            </table>';
        }

        // Sección de Controversias
        if ($proceso->controversias()->count() > 0) {
            $html .= '<h3 style="font-size: 18px; color: #2C3E50; margin: 25px 0 15px; padding-bottom: 5px; border-bottom: 2px solid #2C3E50;">CONTROVERSIAS</h3>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Cédula Beneficiario</th>
                        <th>Categoría</th>
                        <th>Valor Controversia</th>
                    </tr>
                </thead>
                <tbody>';
            
            foreach ($proceso->controversias as $controversia) {
                $html .= '<tr>
                    <td>' . $controversia->cedula . '</td>
                    <td>' . ($controversia->categoriaControversia->nombre ?? 'N/A') . '</td>
                    <td class="amount">$ ' . number_format($controversia->valor_controversia, 2, ',', '.') . '</td>
                </tr>';
            }
            
            $html .= '</tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="2" class="amount"><strong>Subtotal Controversias:</strong></td>
                        <td class="amount"><strong>$ ' . number_format($totalControversias, 2, ',', '.') . '</strong></td>
                    </tr>
                </tfoot>
            </table>';
        }

        // Total General
        $html .= '<div class="total-section">
            <div class="total-amount">
                TOTAL GENERAL: $ ' . number_format($proceso->total_general, 2, ',', '.') . '
            </div>
            <div style="margin-top: 10px; color: #666;">
                Pesos Colombianos
            </div>
        </div>

        <div class="footer">
            <p>Sistema Jurídico de Tránsito - Factura No. ' . str_pad($proceso->id, 6, '0', STR_PAD_LEFT) . ' - Página 1 de 1</p>
            <p>Generado automáticamente el ' . now()->format('d/m/Y H:i:s') . '</p>
        </div>
        
        <script>
            // Auto-imprimir al cargar la página (opcional)
            // window.onload = function() {
            //     window.print();
            // };
        </script>
    </body>
    </html>';

        return $html;
    }

    private function generateConsolidatedReport($procesos)
    {
        $totalGeneral = $procesos->sum('total_general');
        $totalClientes = $procesos->where('tipo_usuario', 'cliente')->count();
        $totalTramitadores = $procesos->where('tipo_usuario', 'tramitador')->count();

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Consolidado de Procesos</title>
    <style>
        @media print {
            @page {
                margin: 20mm 15mm;
            }
            
            body {
                margin: 0;
                padding: 0;
                font-size: 12pt;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-instructions {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 20px;
            padding: 0;
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
            font-size: 24px;
            font-weight: bold;
            color: #2C3E50;
            margin: 0;
        }
        
        .invoice-title {
            font-size: 22px;
            color: #2C3E50;
            margin: 0;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .summary-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            text-align: center;
        }
        
        .summary-label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2C3E50;
            margin-top: 5px;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        
        .detail-table th {
            background-color: #2C3E50;
            color: white;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .detail-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        
        .detail-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .amount {
            text-align: right;
        }
        
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #2C3E50;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #666;
            font-size: 10px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
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
        
        .print-instructions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .print-btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 0;
            cursor: pointer;
            border: none;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="print-instructions no-print">
        <strong>Instrucciones para guardar como PDF:</strong><br>
        1. Haz clic en el botón "Imprimir"<br>
        2. En el diálogo de impresión, selecciona "Guardar como PDF" como impresora<br>
        3. Configura el tamaño de página a A4 y márgenes a 20mm<br>
        4. Haz clic en "Guardar"<br>
        <button class="print-btn" onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>

    <div class="invoice-header">
        <div class="company-info">
            <h1 class="company-name">SISTEMA JURÍDICO DE TRÁNSITO</h1>
            <div style="color: #666;">
                <div>Reporte Consolidado de Procesos</div>
                <div>Fecha de generación: ' . now()->format('d/m/Y H:i:s') . '</div>
            </div>
        </div>
        <div class="invoice-info">
            <h2 class="invoice-title">REPORTE FACTURA</h2>
            <div class="invoice-number">No. ' . now()->format('YmdHis') . '</div>
        </div>
    </div>

    <div class="summary-section">
        <div class="summary-item">
            <div class="summary-label">Total Procesos</div>
            <div class="summary-value">' . $procesos->count() . '</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Procesos Clientes</div>
            <div class="summary-value">' . $totalClientes . '</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Procesos Tramitadores</div>
            <div class="summary-value">' . $totalTramitadores . '</div>
        </div>
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Cliente/Tramitador</th>
                <th>Cédula Beneficiario</th>
                <th>Servicio</th>
                <th>Total Cursos</th>
                <th>Total Renovaciones</th>
                <th>Total Licencias</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>';
        
        foreach ($procesos as $proceso) {
            $clienteNombre = $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->nombre ?? '-' : '-';
            $clienteCedula = $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->cedula ?? '-' : '-';
            $tramitadorNombre = $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->nombre ?? '-' : '-';
            $tramitadorCedula = $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->cedula ?? '-' : '-';
            $cedulaBeneficiario = $proceso->cedula_completa; // Usar el método corregido
            
            // Obtener el texto del servicio
            $servicioText = match($proceso->tipo_servicio) {
                'curso' => 'Curso',
                'renovacion' => 'Renovación',
                'licencia' => 'Licencia',
                'traspaso' => 'Traspaso',
                'runt' => 'RUNT',
                'controversia' => 'Controversia',
                default => ucfirst($proceso->tipo_servicio),
            };
            
            $html .= '<tr>
                <td>' . $proceso->id . '</td>
                <td>' . $proceso->created_at->format('d/m/Y') . '</td>
                <td>';
            
            if ($proceso->tipo_usuario == 'cliente') {
                $html .= '<span class="badge badge-cliente">Cliente</span>';
            } else {
                $html .= '<span class="badge badge-tramitador">Tramitador</span>';
            }
            
            $html .= '</td>
                <td>' . ($proceso->tipo_usuario == 'cliente' ? $clienteNombre : $tramitadorNombre) . '</td>
                <td>' . $cedulaBeneficiario . '</td>
                <td>' . $servicioText . '</td>
                <td class="amount">$ ' . number_format($proceso->cursos()->sum('valor_recibir'), 2, ',', '.') . '</td>
                <td class="amount">$ ' . number_format($proceso->renovaciones()->sum('valor_total'), 2, ',', '.') . '</td>
                <td class="amount">$ ' . number_format($proceso->licencias()->sum('valor_total_licencia'), 2, ',', '.') . '</td>
                <td class="amount"><strong>$ ' . number_format($proceso->total_general, 2, ',', '.') . '</strong></td>
            </tr>';
        }
        
        $html .= '</tbody>
        <tfoot>
            <tr style="background-color: #e8f5e8; font-weight: bold;">
                <td colspan="6" style="text-align: right;">TOTAL GENERAL:</td>
                <td class="amount">$ ' . number_format($procesos->sum(fn($p) => $p->cursos()->sum('valor_recibir')), 2, ',', '.') . '</td>
                <td class="amount">$ ' . number_format($procesos->sum(fn($p) => $p->renovaciones()->sum('valor_total')), 2, ',', '.') . '</td>
                <td class="amount">$ ' . number_format($procesos->sum(fn($p) => $p->licencias()->sum('valor_total_licencia')), 2, ',', '.') . '</td>
                <td class="amount">$ ' . number_format($totalGeneral, 2, ',', '.') . '</td>
            </tr>
        </tfoot>
    </table>

    <div class="total-section">
        <div class="total-amount">
            TOTAL CONSOLIDADO: $ ' . number_format($totalGeneral, 2, ',', '.') . '
        </div>
        <div style="margin-top: 5px; color: #666; font-size: 11px;">
            Pesos Colombianos (COP)
        </div>
    </div>

    <div class="footer">
        <p>Sistema Jurídico de Tránsito - Factura Consolidada de Procesos - Página 1 de 1</p>
        <p>Generado automáticamente el ' . now()->format('d/m/Y H:i:s') . '</p>
    </div>
    
    <script>
        // Auto-imprimir al cargar la página (opcional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>';

        return $html;
    }
}