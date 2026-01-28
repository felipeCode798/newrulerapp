<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->nullable()->unique();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('contacto')->nullable();
            $table->string('celular_contacto')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('cia_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cia_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_controversia_id')->constrained()->cascadeOnDelete();
            $table->decimal('precio', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->unique(['cia_id', 'categoria_controversia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cia_precios');
        Schema::dropIfExists('cias');
    }
};