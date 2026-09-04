<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();

            // Contexto de la petición
            $table->string('request_id', 36)->nullable()->index()
                  ->comment('X-Request-ID propagado a toda la operación');
            $table->string('session_uuid', 36)->nullable()->index();

            // Actor
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_nombre', 200)->nullable()
                  ->comment('Snapshot del nombre al momento del evento');
            $table->string('user_email', 200)->nullable();
            $table->string('user_rol', 100)->nullable();

            // Contexto organizacional
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('sede_nombre', 200)->nullable();

            // Evento
            $table->string('modulo', 60)
                  ->comment('alumnos, calificaciones, pagos, caja, horarios, usuarios, etc.');
            $table->string('accion', 60)
                  ->comment('create, update, delete, restore, login, logout, export, print, etc.');
            $table->string('evento', 100)->nullable()
                  ->comment('Nombre descriptivo: alumno.inscripcion, pago.cancelacion, etc.');
            $table->string('descripcion', 500)->nullable()
                  ->comment('Descripción en lenguaje natural del evento');

            // Recurso afectado
            $table->string('model', 100)->nullable()
                  ->comment('App\\Models\\Alumno');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_descripcion', 300)->nullable()
                  ->comment('Snapshot identificador: matrícula, nombre, folio, etc.');

            // Datos antes/después (inmutables)
            $table->json('before_data')->nullable()
                  ->comment('Estado del registro ANTES de la acción');
            $table->json('after_data')->nullable()
                  ->comment('Estado del registro DESPUÉS de la acción');
            $table->json('changes')->nullable()
                  ->comment('Sólo los campos que cambiaron {campo: [antes, despues]}');
            $table->text('motivo')->nullable()
                  ->comment('Motivo ingresado por el usuario para acciones críticas');

            // Permisos utilizados
            $table->string('permission_usado', 100)->nullable();
            $table->string('alcance_permiso', 60)->nullable();

            // Red y dispositivo
            $table->string('ip_address', 45)->nullable();
            $table->string('device_id', 100)->nullable();
            $table->string('device_type', 30)->nullable();
            $table->string('sistema_operativo', 100)->nullable();
            $table->string('navegador', 100)->nullable();
            $table->text('user_agent')->nullable();

            // Geolocalización — el núcleo de la trazabilidad solicitada
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->decimal('precision_metros', 10, 2)->nullable();
            $table->decimal('altitud', 10, 2)->nullable();
            $table->decimal('velocidad', 8, 3)->nullable();
            $table->string('fuente_ubicacion', 30)->nullable()
                  ->comment('gps, network, ip, manual, denied, unavailable');
            $table->boolean('dentro_geocerca')->nullable();

            // Resultado
            $table->string('resultado', 20)->default('success')
                  ->comment('success, failed, unauthorized, error');
            $table->string('motivo_fallo', 300)->nullable();
            $table->integer('http_status')->nullable();
            $table->integer('duracion_ms')->nullable()
                  ->comment('Duración de la operación en milisegundos');

            // Metadatos extra
            $table->json('metadata')->nullable()
                  ->comment('Datos adicionales específicos del módulo');

            $table->timestamp('created_at')->useCurrent();
            // Sin updated_at ni softDeletes — la auditoría es INMUTABLE

            $table->index(['user_id', 'created_at']);
            $table->index(['modulo', 'accion', 'created_at']);
            $table->index(['model', 'model_id']);
            $table->index(['organizacion_id', 'created_at']);
            $table->index(['sede_id', 'created_at']);
            $table->index('resultado');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
