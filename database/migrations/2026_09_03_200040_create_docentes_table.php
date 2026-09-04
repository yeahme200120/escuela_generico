<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('numero_empleado', 30)->nullable()->unique();
            $table->string('especialidad', 200)->nullable();
            $table->string('cedula', 30)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->enum('tipo_contrato', ['base', 'contrato', 'honorarios', 'tiempo_parcial'])->default('contrato');
            $table->enum('estatus', ['activo', 'inactivo', 'baja'])->default('activo');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('docente_grupo_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->tinyInteger('horas_semana')->default(3);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['docente_id', 'grupo_id', 'materia_id', 'ciclo_escolar_id'], 'dgm_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_grupo_materia');
        Schema::dropIfExists('docentes');
    }
};
