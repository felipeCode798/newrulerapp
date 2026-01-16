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
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('categoria');
            $table->decimal('precio_cliente_50_transito', 10, 2)->default(0);
            $table->decimal('precio_cliente_50_recibir', 10, 2)->default(0);
            $table->decimal('precio_cliente_20_transito', 10, 2)->default(0);
            $table->decimal('precio_cliente_20_recibir', 10, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
