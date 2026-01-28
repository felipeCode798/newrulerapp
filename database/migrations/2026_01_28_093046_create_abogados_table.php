<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abogados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('documento')->unique();
            $table->string('especialidad')->default('transito');
            $table->string('tarjeta_profesional')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->decimal('honorarios_hora', 10, 2)->default(0);
            $table->decimal('porcentaje_comision', 5, 2)->default(0);
            $table->text('areas_practica')->nullable();
            $table->text('formacion_academica')->nullable();
            $table->text('experiencia')->nullable();
            $table->boolean('disponible')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abogados');
    }
};