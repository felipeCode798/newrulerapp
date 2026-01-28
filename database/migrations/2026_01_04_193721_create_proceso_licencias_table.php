<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proceso_licencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->string('cedula');
            $table->json('categorias_seleccionadas'); // IDs de categorías
            $table->foreignId('escuela_id')->constrained();
            $table->enum('enrolamiento', ['cruce_pin', 'guardado', 'pagado']);
            $table->decimal('valor_enrolamiento', 10, 2)->nullable();
            $table->foreignId('pin_escuela_id')->nullable()->constrained();
            $table->decimal('valor_carta_escuela', 10, 2)->default(0);
            $table->enum('examen_medico', ['no_aplica', 'pendiente', 'finalizado', 'devuelto'])->default('no_aplica');
            $table->decimal('valor_examen_medico', 10, 2)->default(0);
            $table->enum('impresion', ['no_aplica', 'pendiente', 'finalizado', 'devuelto'])->default('no_aplica');
            $table->decimal('valor_impresion', 10, 2)->default(0);
            $table->enum('sin_curso', ['no_aplica', 'pendiente', 'finalizado', 'devuelto'])->default('no_aplica');
            $table->decimal('valor_sin_curso', 10, 2)->default(0);
            $table->decimal('valor_total_licencia', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'enviado', 'en_proceso', 'finalizado'])->default('pendiente');
            $table->text('descripcion_general')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_licencias');
    }
};
