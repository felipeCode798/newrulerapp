<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tramitador extends Model
{
    protected $table = 'tramitadores';

    protected $fillable = [
        'user_id',
        'nombre',
        'cedula',
        'email',
        'telefono',
        'curso_50_transito',
        'curso_50_recibir',
        'curso_20_transito',
        'curso_20_recibir',
        'activo',
    ];

    protected $casts = [
        'curso_50_transito' => 'decimal:2',
        'curso_50_recibir' => 'decimal:2',
        'curso_20_transito' => 'decimal:2',
        'curso_20_recibir' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function procesos(): HasMany
    {
        return $this->hasMany(Proceso::class);
    }

    public function renovaciones(): BelongsToMany
    {
        return $this->belongsToMany(Renovacion::class, 'tramitador_renovacion')
            ->withPivot([
                'precio_renovacion',  // Cambiado de 'precio_tramitador'
                'precio_examen',
                'precio_lamina'
            ])
            ->withTimestamps();
    }

    public function controversias(): BelongsToMany
    {
        return $this->belongsToMany(CategoriaControversia::class, 'tramitador_controversia')
            ->withPivot('precio_tramitador')
            ->withTimestamps();
    }

    // Nueva relación para categorías
    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'tramitador_categoria')
            ->withPivot('precio')
            ->withTimestamps();
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'tramitador_curso')
            ->withPivot([
                'precio_50_transito',
                'precio_50_recibir',
                'precio_20_transito',
                'precio_20_recibir'
            ])
            ->withTimestamps();
    }

    public function cursoPrecios()
    {
        return $this->hasMany(TramitadorCurso::class);
    }

    public function renovacionPrecios()
    {
        return $this->hasMany(TramitadorRenovacion::class);
    }

    public function controversiaPrecios()
    {
        return $this->hasMany(TramitadorControversia::class);
    }

    public function categoriaPrecios()
    {
        return $this->hasMany(TramitadorCategoria::class);
    }
}
