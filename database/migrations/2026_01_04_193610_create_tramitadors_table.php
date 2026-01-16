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
        Schema::create('tramitadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->string('cedula')->unique();
            $table->string('email')->unique();
            $table->string('telefono');

            // Precios para cursos
            $table->decimal('curso_50_transito', 10, 2)->default(0);
            $table->decimal('curso_50_recibir', 10, 2)->default(0);
            $table->decimal('curso_20_transito', 10, 2)->default(0);
            $table->decimal('curso_20_recibir', 10, 2)->default(0);

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramitadors');
    }
};
