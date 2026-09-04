<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escuela_id')->constrained('escuelas')->cascadeOnDelete();
            $table->string('clave', 30)->nullable();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->tinyInteger('horas_semana')->default(3);
            $table->tinyInteger('creditos')->default(5);
            $table->enum('tipo', ['obligatoria', 'optativa', 'taller', 'extracurricular'])->default('obligatoria');
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['escuela_id', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
