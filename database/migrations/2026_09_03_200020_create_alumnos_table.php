<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->string('matricula', 30)->nullable()->unique();
            $table->string('curp', 18)->nullable()->unique();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F', 'otro'])->nullable();
            $table->string('email', 200)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->text('direccion')->nullable();
            $table->string('foto', 255)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->enum('estatus', ['activo', 'baja_temporal', 'baja_definitiva', 'egresado'])->default('activo');
            $table->enum('situacion_academica', ['regular', 'irregular', 'reprobado', 'en_regularizacion', 'condicionado'])->default('regular');
            $table->enum('situacion_inscripcion', ['inscrito', 'reinscrito', 'pendiente', 'no_reinscrito', 'cancelada'])->default('pendiente');
            $table->enum('estatus_riesgo', ['normal', 'observacion', 'riesgo_medio', 'riesgo_alto'])->default('normal');
            $table->foreignId('sede_actual_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organizacion_id', 'estatus']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
