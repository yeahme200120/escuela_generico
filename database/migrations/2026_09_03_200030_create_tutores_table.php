<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutores', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('parentesco', 50)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('telefono_trabajo', 30)->nullable();
            $table->string('ocupacion', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('alumno_tutor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('tutores')->cascadeOnDelete();
            $table->boolean('es_principal')->default(false);
            $table->boolean('autorizado_recoger')->default(true);
            $table->timestamps();

            $table->unique(['alumno_id', 'tutor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_tutor');
        Schema::dropIfExists('tutores');
    }
};
