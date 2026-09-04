<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();

            // Usuario (nullable: intentos fallidos pueden no tener user_id válido)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email_intento', 200)->nullable()
                  ->comment('Email usado en el intento (para fallos)');
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('session_uuid', 36)->nullable()->index();

            // Tipo y resultado
            $table->string('evento', 50)
                  ->comment('login, logout, login_failed, token_refresh, 2fa_challenge, 2fa_failed, password_reset, session_revoked');
            $table->string('resultado', 20)
                  ->comment('success, failed, blocked, expired');
            $table->string('motivo_rechazo', 200)->nullable()
                  ->comment('credenciales_invalidas, cuenta_inactiva, cuenta_bloqueada, 2fa_fallido, etc.');

            // Red y dispositivo
            $table->string('ip_address', 45)->nullable();
            $table->string('device_id', 100)->nullable();
            $table->string('device_type', 30)->nullable();
            $table->string('sistema_operativo', 100)->nullable();
            $table->string('navegador', 100)->nullable();
            $table->text('user_agent')->nullable();

            // Geolocalización
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->decimal('precision_metros', 10, 2)->nullable();
            $table->string('fuente_ubicacion', 30)->nullable();

            // Detección de anomalías
            $table->boolean('es_nuevo_dispositivo')->default(false);
            $table->boolean('es_nueva_ubicacion')->default(false);
            $table->boolean('fuera_de_geocerca')->default(false);
            $table->boolean('fuera_de_horario')->default(false);
            $table->boolean('viaje_imposible')->default(false)
                  ->comment('Velocidad físicamente imposible entre dos accesos');
            $table->decimal('distancia_ultimo_acceso_km', 10, 3)->nullable();
            $table->integer('minutos_desde_ultimo_acceso')->nullable();

            $table->string('request_id', 36)->nullable()->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['resultado', 'created_at']);
            $table->index('ip_address');
            $table->index(['evento', 'resultado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
