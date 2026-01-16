<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('tramitador_categoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramitador_id')->constrained('tramitadores')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->decimal('precio', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['tramitador_id', 'categoria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramitador_categoria');
        Schema::dropIfExists('categorias');
    }
};
