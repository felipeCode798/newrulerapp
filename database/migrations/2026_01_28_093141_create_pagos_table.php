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
            $table->foreignId('proceso_id')->constrained()->cascadeOnDelete();
            $table->decimal('valor', 10, 2);
            $table->enum('metodo', ['efectivo', 'transferencia', 'tarjeta', 'cheque', 'otro']);
            $table->string('referencia')->nullable();
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};