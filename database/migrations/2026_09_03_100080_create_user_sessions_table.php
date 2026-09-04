<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete()
                  ->comment('Sede activa al momento del login');

            // Token / Sanctum
            $table->string('token_id', 100)->nullable()->index()
                  ->comment('ID del personal access token de Sanctum');
            $table->string('sanctum_token_hash', 64)->nullable();

            // Dispositivo
            $table->string('device_id', 100)->nullable()->index()
                  ->comment('Fingerprint del dispositivo calculado en frontend');
            $table->string('device_name', 200)->nullable();
            $table->string('device_type', 30)->nullable()
                  ->comment('desktop, mobile, tablet, unknown');
            $table->string('sistema_operativo', 100)->nullable();
            $table->string('navegador', 100)->nullable();
            $table->string('version_navegador', 50)->nullable();
            $table->text('user_agent')->nullable();

            // Red
            $table->string('ip_address', 45)->nullable();
            $table->string('ip_country', 10)->nullable();
            $table->string('ip_city', 100)->nullable();

            // Geolocalización (capturada desde el navegador)
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->decimal('precision_metros', 10, 2)->nullable();
            $table->decimal('altitud', 10, 2)->nullable();
            $table->string('fuente_ubicacion', 30)->nullable()
                  ->comment('gps, network, ip, manual, denied, unavailable');

            // Pantalla / hardware
            $table->integer('pantalla_ancho')->nullable();
            $table->integer('pantalla_alto')->nullable();
            $table->string('zona_horaria', 60)->nullable();
            $table->string('idioma', 20)->nullable();
            $table->decimal('pixel_ratio', 5, 2)->nullable();
            $table->boolean('es_touch')->default(false);

            // Tiempos
            $table->timestamp('first_seen_at')->useCurrent();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_reason', 200)->nullable()
                  ->comment('logout, admin_revoke, timeout, suspicious, password_change');

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index(['user_id', 'active']);
            $table->index(['user_id', 'device_id']);
            $table->index('ip_address');
            $table->index('first_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
