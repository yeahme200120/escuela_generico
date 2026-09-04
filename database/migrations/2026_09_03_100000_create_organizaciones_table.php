<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->string('nombre', 200);
            $table->string('razon_social', 250)->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('clave', 50)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('slogan', 300)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('pais', 100)->default('México');
            $table->string('codigo_postal', 10)->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->boolean('activa')->default(true);
            $table->boolean('modulo_finanzas_activo')->default(true);
            $table->boolean('modulo_rh_activo')->default(true);
            $table->boolean('modulo_inventario_activo')->default(true);
            $table->boolean('modulo_admisiones_activo')->default(false);
            $table->boolean('permite_modo_oscuro')->default(true);
            $table->json('configuracion')->nullable()->comment('Configuración general en JSON');
            $table->timestamps();
            $table->softDeletes();

            $table->index('activa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};
