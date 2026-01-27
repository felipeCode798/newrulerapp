<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramitador_renovacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramitador_id')->constrained('tramitadores')->cascadeOnDelete();
            $table->foreignId('renovacion_id')->constrained('renovaciones')->cascadeOnDelete();
            $table->decimal('precio_renovacion', 10, 2)->default(0);
            $table->decimal('precio_examen', 10, 2)->default(0);
            $table->decimal('precio_lamina', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['tramitador_id', 'renovacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramitador_renovacion');
    }
};