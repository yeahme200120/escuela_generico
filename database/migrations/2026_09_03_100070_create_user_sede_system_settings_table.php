<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Relación usuario ↔ sedes (multisede)
        Schema::create('user_sedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'sede_id']);
            $table->index(['user_id', 'activo']);
        });

        // Configuración general del sistema por organización (tema, flags, etc.)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()
                  ->constrained('organizaciones')->cascadeOnDelete();
            $table->string('key', 120);
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string')
                  ->comment('string, boolean, integer, json, color, file');
            $table->string('grupo', 60)->nullable()
                  ->comment('theme, academico, finanzas, seguridad, etc.');
            $table->text('descripcion')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['organizacion_id', 'key']);
            $table->index(['organizacion_id', 'grupo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('user_sedes');
    }
};
