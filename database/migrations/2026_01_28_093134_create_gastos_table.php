<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->decimal('valor', 10, 2);
            $table->foreignId('proceso_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cia_id')->nullable()->constrained('cias')->nullOnDelete();
            $table->foreignId('abogado_id')->nullable()->constrained('abogados')->nullOnDelete();
            $table->enum('tipo_pago', ['cia', 'abogado', 'general', 'otro']);
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->date('fecha_gasto');
            $table->string('comprobante')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};