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
        Schema::create('proceso_traspasos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->string('cedula');
            $table->string('nombre_propietario');
            $table->string('nombre_comprador');
            $table->string('cedula_comprador');
            $table->decimal('derecho_traspaso', 10, 2)->default(0);
            $table->decimal('porcentaje', 10, 2)->default(0);
            $table->decimal('honorarios', 10, 2)->default(0);
            $table->decimal('comision', 10, 2)->default(0);
            $table->decimal('total_recibir', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_traspasos');
    }
};
