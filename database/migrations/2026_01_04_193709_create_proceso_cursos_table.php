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
        Schema::create('proceso_cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained();
            $table->string('cedula');
            $table->string('nombre')->nullable();
            $table->string('numero_comparendo')->nullable();
            $table->foreignId('cia_id')->nullable()->constrained('cias');
            $table->enum('estado', ['pendiente', 'enviado', 'en_proceso', 'finalizado'])->default('pendiente');
            $table->text('descripcion_general')->nullable();
            $table->enum('porcentaje', ['50', '20']);
            $table->decimal('valor_transito', 10, 2)->default(0);
            $table->decimal('valor_recibir', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_cursos');
    }
};
