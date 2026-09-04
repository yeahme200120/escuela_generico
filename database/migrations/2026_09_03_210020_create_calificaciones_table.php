<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Periodos de evaluación ────────────────────────────────────
        Schema::create('periodos_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->string('nombre', 100)->comment('Ej: 1er Parcial');
            $table->tinyInteger('numero');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->date('fecha_cierre')->nullable();
            $table->boolean('cerrado')->default(false);
            $table->timestamp('cerrado_at')->nullable();
            $table->foreignId('cerrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['ciclo_escolar_id']);
        });

        // ── Calificaciones ───────────────────────────────────────────
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->foreignId('periodo_evaluacion_id')->constrained('periodos_evaluacion')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->decimal('calificacion', 5, 2)->nullable();
            $table->string('calificacion_letra', 5)->nullable()->comment('A,B,C,D,F o similar');
            $table->enum('resultado', ['aprobado', 'reprobado', 'np', 'na', 'extraordinario', 'regularizado'])->nullable();
            $table->string('observaciones', 300)->nullable();
            $table->foreignId('usuario_registra')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_actualiza')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['alumno_id', 'materia_id', 'periodo_evaluacion_id'], 'calificacion_unica');
            $table->index(['alumno_id', 'ciclo_escolar_id']);
            $table->index(['grupo_id', 'periodo_evaluacion_id']);
        });

        // ── Regularizaciones ─────────────────────────────────────────
        Schema::create('regularizaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->decimal('calificacion_original', 5, 2)->nullable();
            $table->decimal('calificacion_regularizacion', 5, 2)->nullable();
            $table->date('fecha')->nullable();
            $table->enum('resultado', ['aprobado', 'reprobado', 'pendiente'])->default('pendiente');
            $table->string('observaciones', 300)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['alumno_id', 'ciclo_escolar_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regularizaciones');
        Schema::dropIfExists('calificaciones');
        Schema::dropIfExists('periodos_evaluacion');
    }
};
