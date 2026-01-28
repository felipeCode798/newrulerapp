<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $fillable = [
        'proceso_id',
        'valor',
        'metodo',
        'referencia',
        'fecha_pago',
        'observaciones',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(Proceso::class);
    }

    public function calcularSaldoProceso(): void
    {
        $totalPagos = $this->proceso->pagos()->sum('valor');
        $totalProceso = $this->proceso->total_general;
        
        if ($totalPagos >= $totalProceso) {
            $this->proceso->update(['pagado' => true]);
        }
    }
}