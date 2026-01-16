<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PinEscuela extends Model
{
    protected $fillable = [
        'escuela_id',
        'user_id',
        'pin',
        'estado',
    ];

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
