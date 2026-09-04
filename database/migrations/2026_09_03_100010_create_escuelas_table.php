<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escuelas', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->constrained('organizaciones')->restrictOnDelete();
            $table->string('nombre', 200);
            $table->string('clave', 50)->nullable();
            $table->string('clave_sep', 50)->nullable()->comment('Clave oficial SEP');
            $table->string('tipo_sostenimiento')->default('privado')
                  ->comment('publico, privado, autonomo, concertado');
            $table->string('nivel_sistema')->nullable()
                  ->comment('basica, media_superior, superior, capacitacion');
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('estado', 100)->nullable();
            $table->string('pais', 100)->default('México');
            $table->boolean('activa')->default(true);
            $table->json('configuracion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organizacion_id', 'activa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escuelas');
    }
};
