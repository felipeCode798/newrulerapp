// database/migrations/xxxx_create_envio_facturas_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envio_facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proceso_id')->constrained()->onDelete('cascade');
            $table->string('metodo')->default('descargar'); // email, whatsapp, descargar
            $table->string('email_destino')->nullable();
            $table->string('telefono_destino')->nullable();
            $table->text('mensaje')->nullable();
            $table->string('ruta_archivo')->nullable();
            $table->string('url_descarga')->nullable();
            $table->foreignId('enviado_por')->constrained('users')->onDelete('cascade');
            $table->timestamp('fecha_envio')->nullable();
            $table->string('estado')->default('pendiente'); // pendiente, enviado, error
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envio_facturas');
    }
};