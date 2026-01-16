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
