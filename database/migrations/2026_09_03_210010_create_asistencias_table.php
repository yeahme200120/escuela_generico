<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Periodos de asistencia ────────────────────────────────────
        Schema::create('periodos_asistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // ── Asistencias ──────────────────────────────────────────────
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('materia_id')->nullable()->constrained('materias')->nullOnDelete();
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->date('fecha');
            $table->enum('estado', ['presente', 'falta', 'retardo', 'justificada'])->default('presente');
            $table->time('hora_registro')->nullable();
            $table->string('observacion', 300)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            // Sin updated_at

            $table->index(['alumno_id', 'fecha']);
            $table->index(['grupo_id', 'fecha']);
            $table->index(['ciclo_escolar_id', 'estado']);
        });

        // ── Justificantes ────────────────────────────────────────────
        Schema::create('justificantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->string('motivo', 300);
            $table->string('documento', 255)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->foreignId('solicitado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('autorizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['alumno_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificantes');
        Schema::dropIfExists('asistencias');
        Schema::dropIfExists('periodos_asistencia');
    }
};
