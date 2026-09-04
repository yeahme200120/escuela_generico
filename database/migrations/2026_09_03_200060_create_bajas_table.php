<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bajas', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->enum('tipo', ['temporal', 'definitiva', 'desercion', 'traslado', 'egreso'])->default('temporal');
            $table->date('fecha_solicitud');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin_estimada')->nullable();
            $table->date('fecha_reingreso')->nullable();
            $table->string('motivo', 500);
            $table->enum('motivo_desercion', [
                'abandono',
                'inasistencia_prolongada',
                'problemas_economicos',
                'problemas_familiares',
                'cambio_ciudad',
                'cambio_escuela',
                'bajo_aprovechamiento',
                'motivo_personal',
                'otro',
            ])->nullable();
            $table->string('documento', 255)->nullable();
            $table->enum('estatus', ['solicitada', 'aprobada', 'activa', 'reingresado', 'cancelada'])->default('solicitada');
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_solicita')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_autoriza')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['alumno_id', 'tipo', 'estatus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bajas');
    }
};
