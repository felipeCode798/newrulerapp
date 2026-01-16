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
        Schema::create('proceso_runts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->string('cedula');
            $table->string('numero');
            $table->decimal('comision', 10, 2)->default(0);
            $table->decimal('pago', 10, 2)->default(0);
            $table->decimal('honorarios', 10, 2)->default(0);
            $table->decimal('valor_recibir', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_runts');
    }
};
