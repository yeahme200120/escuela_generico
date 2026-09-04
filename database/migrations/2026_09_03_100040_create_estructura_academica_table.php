<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ciclos escolares
        Schema::create('ciclos_escolares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->restrictOnDelete();
            $table->foreignId('escuela_id')->constrained('escuelas')->restrictOnDelete();
            $table->string('nombre', 100)->comment('Ej: 2026-2027');
            $table->string('clave', 30)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('estatus')->default('configuracion')
                  ->comment('configuracion, activo, cerrado, archivado');
            $table->boolean('es_actual')->default(false);
            $table->json('periodos')->nullable()->comment('Definición de periodos/parciales en JSON');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['escuela_id', 'nombre']);
            $table->index(['escuela_id', 'estatus']);
        });

        // Niveles educativos
        Schema::create('niveles_educativos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escuela_id')->constrained('escuelas')->restrictOnDelete();
            $table->string('nombre', 100)->comment('Preescolar, Primaria, Secundaria, etc.');
            $table->string('clave', 30)->nullable();
            $table->integer('orden')->default(0);
            $table->integer('duracion_anos')->default(3);
            $table->decimal('calificacion_minima', 5, 2)->nullable()
                  ->comment('Si es null, hereda de la sede');
            $table->decimal('calificacion_maxima', 5, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['escuela_id', 'activo']);
        });

        // Grados
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nivel_educativo_id')->constrained('niveles_educativos')->restrictOnDelete();
            $table->string('nombre', 100)->comment('1°, 2°, 3°, etc.');
            $table->string('clave', 30)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['nivel_educativo_id', 'activo']);
        });

        // Grupos
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->restrictOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->restrictOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->restrictOnDelete();
            $table->foreignId('aula_principal_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->unsignedBigInteger('docente_tutor_id')->nullable();
            $table->string('nombre', 50)->comment('A, B, C, etc.');
            $table->string('turno')->default('matutino')
                  ->comment('matutino, vespertino, nocturno, mixto');
            $table->integer('capacidad')->default(30);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sede_id', 'ciclo_escolar_id', 'activo']);
            $table->index('grado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('grados');
        Schema::dropIfExists('niveles_educativos');
        Schema::dropIfExists('ciclos_escolares');
    }
};
