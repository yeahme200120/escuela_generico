<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();

            // Datos personales
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('email')->unique();
            $table->string('username', 60)->unique()->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('avatar')->nullable();

            // Autenticación
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();

            // Estado
            $table->boolean('activo')->default(true);
            $table->timestamp('ultimo_acceso_at')->nullable();
            $table->string('ultimo_ip', 45)->nullable();
            $table->integer('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();

            // Preferencias
            $table->string('tema_preferido')->default('light')->comment('light, dark, system');
            $table->string('locale', 10)->default('es');
            $table->string('zona_horaria', 60)->default('America/Mexico_City');

            // 2FA
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['organizacion_id', 'activo']);
            $table->index('email');
            $table->index('username');
        });

        // Tabla de restablecimiento de contraseña (reemplaza la default de Laravel)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
