<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->restrictOnDelete();
            $table->foreignId('escuela_id')->constrained('escuelas')->restrictOnDelete();
            $table->string('nombre', 200);
            $table->string('clave', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('pais', 100)->default('México');
            $table->string('codigo_postal', 10)->nullable();

            // Geolocalización de la sede
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->integer('radio_geocerca_metros')->default(500)
                  ->comment('Radio en metros para geocerca de asistencia/acceso');
            $table->boolean('geocerca_activa')->default(false);

            // Configuración académica
            $table->decimal('calificacion_minima', 5, 2)->default(6.00);
            $table->decimal('calificacion_maxima', 5, 2)->default(10.00);
            $table->integer('tolerancia_retardo_minutos')->default(10);
            $table->string('zona_horaria', 60)->default('America/Mexico_City');
            $table->string('moneda', 10)->default('MXN');

            $table->boolean('activa')->default(true);
            $table->json('configuracion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organizacion_id', 'escuela_id', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};
