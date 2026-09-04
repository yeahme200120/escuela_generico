<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edificios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->restrictOnDelete();
            $table->string('nombre', 100);
            $table->string('clave', 30)->nullable();
            $table->integer('numero_pisos')->default(1);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['sede_id', 'activo']);
        });

        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->restrictOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('edificios')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('clave', 30)->nullable();
            $table->string('tipo')->default('salon')
                  ->comment('salon, laboratorio, taller, sala_computo, sala_usos_multiples, auditorio');
            $table->integer('capacidad')->default(30);
            $table->integer('piso')->nullable();
            $table->boolean('tiene_proyector')->default(false);
            $table->boolean('tiene_ac')->default(false);
            $table->boolean('activa')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['sede_id', 'activa']);
            $table->index('edificio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
        Schema::dropIfExists('edificios');
    }
};
