<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Bloques de horario ────────────────────────────────────────
        Schema::create('horario_bloques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->string('nombre', 100)->comment('Ej: Bloque 1');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->tinyInteger('dia_semana')->comment('1=lunes...5=viernes');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['sede_id', 'ciclo_escolar_id']);
        });

        // ── Horarios ─────────────────────────────────────────────────
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('horario_bloque_id')->constrained('horario_bloques')->cascadeOnDelete();
            $table->tinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('publicado')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['grupo_id', 'ciclo_escolar_id']);
            $table->index(['docente_id', 'dia_semana']);
            $table->unique(['docente_id', 'dia_semana', 'hora_inicio', 'ciclo_escolar_id'], 'horario_docente_unique');
            $table->unique(['aula_id', 'dia_semana', 'hora_inicio', 'ciclo_escolar_id'], 'horario_aula_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
        Schema::dropIfExists('horario_bloques');
    }
};
