<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'valor',
        'metodo',
        'referencia',
        'fecha_pago',
        'observaciones',
        'registrado_por',
        'estado',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public static function calcularSaldoProceso(Proceso $proceso): array
    {
        // Verificar si la relación existe
        if (!method_exists($proceso, 'pagos')) {
            return [
                'total_proceso' => $proceso->total_general,
                'total_pagado' => 0,
                'saldo_pendiente' => $proceso->total_general,
                'porcentaje_pagado' => 0,
                'completamente_pagado' => false,
            ];
        }
        
        $totalPagos = $proceso->pagos()->sum('valor');
        $totalProceso = $proceso->total_general;
        $saldoPendiente = $totalProceso - $totalPagos;
        $porcentajePagado = $totalProceso > 0 ? ($totalPagos / $totalProceso) * 100 : 0;
        
        return [
            'total_proceso' => $totalProceso,
            'total_pagado' => $totalPagos,
            'saldo_pendiente' => $saldoPendiente,
            'porcentaje_pagado' => $porcentajePagado,
            'completamente_pagado' => $saldoPendiente <= 0,
        ];
    }
}