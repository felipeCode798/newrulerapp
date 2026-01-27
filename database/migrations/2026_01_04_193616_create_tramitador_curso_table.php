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
        Schema::create('tramitador_curso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramitador_id')->constrained('tramitadores')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->decimal('precio_50_transito', 10, 2)->default(0);
            $table->decimal('precio_50_recibir', 10, 2)->default(0);
            $table->decimal('precio_20_transito', 10, 2)->default(0);
            $table->decimal('precio_20_recibir', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['tramitador_id', 'curso_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramitador_curso');
    }
};