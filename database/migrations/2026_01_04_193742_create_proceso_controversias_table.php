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
        Schema::create('proceso_controversias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_controversia_id')->constrained();
            $table->string('cedula');
            $table->string('nombre')->nullable();
            $table->string('comparendo')->nullable();
            $table->decimal('precio_cia', 10, 2)->nullable();
            $table->string('celular')->nullable();
            $table->boolean('debe')->default(false);
            $table->enum('estado', ['pendiente', 'enviado', 'en_proceso', 'finalizado'])->default('pendiente');
            $table->text('descripcion_general')->nullable();
            $table->decimal('valor_controversia', 10, 2)->default(0);
            $table->dateTime('fecha_hora_cita');
            $table->string('codigo_controversia');
            $table->decimal('venta_controversia', 10, 2)->default(0);
            $table->string('documento_identidad')->nullable();
            $table->string('poder')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_controversias');
    }
};
