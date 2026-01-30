<?php

namespace App\Filament\Resources\ProcesoResource\Widgets;

use App\Models\Proceso;
use App\Models\Gasto;
use App\Models\Pago;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProcesoStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Obtener estadísticas financieras
        $valorTotalGeneral = Proceso::sum('total_general');
        $valorTotalGastos = Gasto::sum('valor');
        $valorTotalPagos = Pago::sum('valor');
        
        // Calcular saldo (Valor total - Gastos - Pagos)
        $saldo = $valorTotalGeneral - $valorTotalGastos - $valorTotalPagos;
        
        // Obtener gastos por estado (opcional)
        $gastosPendientes = Gasto::where('estado', 'pendiente')->sum('valor');
        $gastosPagados = Gasto::where('estado', 'pagado')->sum('valor');

        return [
            Stat::make('Valor Total General', '$' . number_format($valorTotalGeneral, 2))
                ->description('Total de todos los procesos')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('primary')
                ->chart($this->getChartData('general'))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ]),
            
            Stat::make('Total Gastos', '$' . number_format($valorTotalGastos, 2))
                ->description('Pendientes: $' . number_format($gastosPendientes, 2))
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger')
                ->chart($this->getChartData('gastos'))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("openModal", { component: "filament.admin.resources.gasto.modals.stats" })',
                ]),
            
            Stat::make('Saldo Neto', '$' . number_format($saldo, 2))
                ->description($this->getSaldoDescription($saldo))
                ->descriptionIcon($this->getSaldoIcon($saldo))
                ->color($this->getSaldoColor($saldo))
                ->chart($this->getChartData('saldo'))
                ->extraAttributes([
                    'class' => 'font-bold',
                ]),

            // Stat opcional para mostrar Total Pagos
            // Stat::make('Total Pagos Recibidos', '$' . number_format($valorTotalPagos, 2))
            //     ->description('Ingresos recibidos')
            //     ->descriptionIcon('heroicon-o-arrow-trending-up')
            //     ->color('success')
            //     ->chart($this->getChartData('pagos'))
            //     ->extraAttributes([
            //         'class' => 'cursor-pointer',
            //     ]),
        ];
    }

    /**
     * Genera datos de gráfico para cada stat
     */
    protected function getChartData(string $type): array
    {
        // Datos de ejemplo para el gráfico
        return match($type) {
            'general' => [12, 14, 15, 18, 20, 22, 25],
            'gastos' => [3, 4, 5, 6, 7, 8, 9],
            'saldo' => [9, 10, 10, 12, 13, 14, 16],
            'pagos' => [2, 3, 4, 5, 6, 7, 8],
            default => [],
        };
    }

    /**
     * Obtiene la descripción del saldo según su valor
     */
    protected function getSaldoDescription(float $saldo): string
    {
        if ($saldo > 0) {
            return 'Saldo positivo a favor';
        } elseif ($saldo < 0) {
            return 'Saldo negativo en contra';
        } else {
            return 'Saldo equilibrado';
        }
    }

    /**
     * Obtiene el icono del saldo según su valor
     */
    protected function getSaldoIcon(float $saldo): string
    {
        if ($saldo > 0) {
            return 'heroicon-o-check-circle';
        } elseif ($saldo < 0) {
            return 'heroicon-o-exclamation-triangle';
        } else {
            return 'heroicon-o-scale';
        }
    }

    /**
     * Obtiene el color del saldo según su valor
     */
    protected function getSaldoColor(float $saldo): string
    {
        if ($saldo > 0) {
            return 'success';
        } elseif ($saldo < 0) {
            return 'danger';
        } else {
            return 'gray';
        }
    }

    // Método opcional para filtrar por fechas
    public static function canView(): bool
    {
        return true;
    }
}