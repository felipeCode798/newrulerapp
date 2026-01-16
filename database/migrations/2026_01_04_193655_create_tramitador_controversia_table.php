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
        Schema::create('tramitador_controversia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tramitador_id')->constrained('tramitadores')->cascadeOnDelete();
            $table->foreignId('categoria_controversia_id')->constrained('categoria_controversias')->cascadeOnDelete();
            $table->decimal('precio_tramitador', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramitador_controversia');
    }
};
