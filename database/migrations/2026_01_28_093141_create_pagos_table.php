// database/migrations/xxxx_create_pagos_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->onDelete('cascade');
            $table->decimal('valor', 12, 2);
            $table->string('metodo')->default('efectivo');
            $table->string('referencia')->nullable();
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->constrained('users')->onDelete('cascade');
            $table->string('estado')->default('pendiente');
            $table->timestamps();
            
            $table->index(['proceso_id', 'fecha_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};