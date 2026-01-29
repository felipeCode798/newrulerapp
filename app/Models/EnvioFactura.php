<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioFactura extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'metodo',
        'email_destino',
        'telefono_destino',
        'mensaje',
        'ruta_archivo',
        'url_descarga',
        'enviado_por',
        'fecha_envio',
        'estado',
        'error',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'enviado_por');
    }
}