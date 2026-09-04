<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_generados', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('ruta_archivo');
            $table->json('filtros')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_generados');
    }
};