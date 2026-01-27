<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proceso_renovaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->foreignId('renovacion_id')->constrained('renovaciones');
            $table->string('cedula');
            $table->boolean('incluye_examen')->default(true);
            $table->boolean('incluye_lamina')->default(true);
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proceso_renovaciones');
    }
};