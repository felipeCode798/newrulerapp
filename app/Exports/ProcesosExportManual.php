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

    public function download($filename, $format = 'csv')
    {
        if ($format === 'xlsx') {
            return $this->downloadExcel($filename);
        }
        
        return $this->downloadCSV($filename);
    }

    protected function downloadExcel($filename)
    {
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function() {
            $this->generateExcel();
        }, 200, $headers);
    }

    protected function downloadCSV($filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() {
            $this->generateCSV();
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function generateExcel()
    {
        // Crear el contenido XML del Excel
        $xml = '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Author>Sistema de Procesos</Author>
        <Created>' . date('c') . '</Created>
    </DocumentProperties>
    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Center"/>
            <Borders/>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="Header">
            <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
            <Interior ss:Color="#4F81BD" ss:Pattern="Solid"/>
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
        </Style>
        <Style ss:ID="Number">
            <NumberFormat ss:Format="#,##0.00"/>
        </Style>
    </Styles>
    <Worksheet ss:Name="Procesos">
        <Table ss:ExpandedColumnCount="15" ss:ExpandedRowCount="' . ($this->procesos->count() + 1) . '" x:FullColumns="1" x:FullRows="1">
            <Column ss:Width="50"/>
            <Column ss:Width="120"/>
            <Column ss:Width="80"/>
            <Column ss:Width="150"/>
            <Column ss:Width="100"/>
            <Column ss:Width="150"/>
            <Column ss:Width="100"/>
            <Column ss:Width="100"/>
            <Column ss:Width="100"/>
            <Column ss:Width="100"/>
            <Column ss:Width="100"/>
            <Column ss:Width="100"/>
            <Column ss:Width="100"/>
            <Column ss:Width="120"/>
            <Column ss:Width="150"/>
            
            <!-- Encabezados -->
            <Row>
                <Cell ss:StyleID="Header"><Data ss:Type="String">ID</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Fecha</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Tipo</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Cliente</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Cédula Cliente</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Tramitador</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Cédula Tramitador</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Cursos</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Renovaciones</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Licencias</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Traspasos</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total RUNT</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Controversias</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">TOTAL GENERAL</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Creado Por</Data></Cell>
            </Row>';

        // Datos
        foreach ($this->procesos as $proceso) {
            // Obtener la cédula correcta según el tipo de usuario
            $cedulaCliente = '-';
            $cedulaTramitador = '-';
            
            if ($proceso->tipo_usuario == 'cliente') {
                $cedulaCliente = $proceso->cliente->cedula ?? '-';
            } else {
                // Para tramitador, mostrar la cédula del beneficiario
                $cedulaCliente = $proceso->cedula_completa;
                $cedulaTramitador = $proceso->tramitador->cedula ?? '-';
            }
            
            $xml .= '
            <Row>
                <Cell><Data ss:Type="Number">' . $proceso->id . '</Data></Cell>
                <Cell><Data ss:Type="String">' . $proceso->created_at->format('d/m/Y H:i') . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($proceso->tipo_usuario == 'cliente' ? 'Cliente' : 'Tramitador') . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($proceso->tipo_usuario == 'cliente' ? ($proceso->cliente->nombre ?? '-') : '-') . '</Data></Cell>
                <Cell><Data ss:Type="String">' . $cedulaCliente . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($proceso->tipo_usuario == 'tramitador' ? ($proceso->tramitador->nombre ?? '-') : '-') . '</Data></Cell>
                <Cell><Data ss:Type="String">' . $cedulaTramitador . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->cursos()->sum('valor_recibir'), 2, '.', '') . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->renovaciones()->sum('valor_total'), 2, '.', '') . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->licencias()->sum('valor_total_licencia'), 2, '.', '') . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->traspasos()->sum('total_recibir'), 2, '.', '') . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->runts()->sum('valor_recibir'), 2, '.', '') . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->controversias()->sum('valor_controversia'), 2, '.', '') . '</Data></Cell>
                <Cell ss:StyleID="Number"><Data ss:Type="Number">' . number_format($proceso->total_general, 2, '.', '') . '</Data></Cell>
                <Cell><Data ss:Type="String">' . ($proceso->createdBy->name ?? '-') . '</Data></Cell>
            </Row>';
        }

        $xml .= '
        </Table>
        <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
            <PageSetup>
                <Header x:Margin="0.3"/>
                <Footer x:Margin="0.3"/>
                <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
            </PageSetup>
            <Selected/>
            <Panes>
                <Pane>
                    <Number>3</Number>
                    <ActiveRow>1</ActiveRow>
                    <ActiveCol>1</ActiveCol>
                </Pane>
            </Panes>
            <ProtectObjects>False</ProtectObjects>
            <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
    </Worksheet>
</Workbook>';

        echo $xml;
    }

    protected function generateCSV()
    {
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
            // Obtener la cédula correcta según el tipo de usuario
            $cedulaCliente = '-';
            $cedulaTramitador = '-';
            
            if ($proceso->tipo_usuario == 'cliente') {
                $cedulaCliente = $proceso->cliente->cedula ?? '-';
            } else {
                // Para tramitador, mostrar la cédula del beneficiario
                $cedulaCliente = $proceso->cedula_completa;
                $cedulaTramitador = $proceso->tramitador->cedula ?? '-';
            }
            
            fputcsv($handle, [
                $proceso->id,
                $proceso->created_at->format('d/m/Y H:i'),
                $proceso->tipo_usuario == 'cliente' ? 'Cliente' : 'Tramitador',
                $proceso->tipo_usuario == 'cliente' ? $proceso->cliente->nombre ?? '-' : '-',
                $cedulaCliente,
                $proceso->tipo_usuario == 'tramitador' ? $proceso->tramitador->nombre ?? '-' : '-',
                $cedulaTramitador,
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
    }
}