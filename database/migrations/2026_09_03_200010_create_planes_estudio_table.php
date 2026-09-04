<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_estudio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escuela_id')->constrained('escuelas')->cascadeOnDelete();
            $table->string('nombre', 200);
            $table->string('clave', 50)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('plan_materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_estudio_id')->constrained('planes_estudio')->cascadeOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->boolean('obligatoria')->default(true);
            $table->tinyInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['plan_estudio_id', 'grado_id', 'materia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_materias');
        Schema::dropIfExists('planes_estudio');
    }
};
