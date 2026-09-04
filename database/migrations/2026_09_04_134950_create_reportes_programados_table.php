<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_programados', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->json('filtros')->nullable();
            $table->string('frecuencia')->nullable();
            $table->timestamp('ultimo_envio')->nullable();
            $table->timestamp('proximo_envio')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_programados');
    }
};