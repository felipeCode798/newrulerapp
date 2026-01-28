<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CiaPrecio extends Model
{
    protected $table = 'cia_precios';
    
    protected $fillable = [
        'cia_id',
        'categoria_controversia_id',
        'precio',
        'observaciones',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function cia(): BelongsTo
    {
        return $this->belongsTo(Cia::class);
    }

    // ESTA RELACIÓN ES LA QUE FALTA
    public function categoriaControversia(): BelongsTo
    {
        return $this->belongsTo(CategoriaControversia::class);
    }
}