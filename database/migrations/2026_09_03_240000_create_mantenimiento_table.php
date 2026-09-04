<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('edificios')->nullOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->unsignedBigInteger('activo_fijo_id')->nullable();
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->enum('prioridad', ['baja','media','alta','urgente'])->default('media');
            $table->enum('estado', ['reportado','en_proceso','resuelto','cancelado'])->default('reportado');
            $table->foreignId('reportado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_reporte');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_resolucion')->nullable();
            $table->decimal('costo', 12, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['sede_id','estado']);
        });

        Schema::create('reingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('grado_id')->nullable()->constrained('grados')->nullOnDelete();
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_solicitud');
            $table->date('fecha_reingreso')->nullable();
            $table->string('motivo', 500);
            $table->enum('estado', ['solicitado','aprobado','completado','rechazado'])->default('solicitado');
            $table->timestamps();
            $table->index(['alumno_id','estado']);
        });

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->string('evento', 100);
            $table->string('url', 500);
            $table->string('metodo', 10)->default('POST');
            $table->json('payload');
            $table->json('headers_respuesta')->nullable();
            $table->integer('codigo_respuesta')->nullable();
            $table->text('respuesta_body')->nullable();
            $table->enum('estado', ['pendiente','enviado','fallido','reintentando'])->default('pendiente');
            $table->integer('intentos')->default(0);
            $table->timestamp('proximo_intento')->nullable();
            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->string('firma', 128)->nullable();
            $table->string('request_id', 36)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->index(['organizacion_id','estado']);
        });

        Schema::create('python_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id', 50)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo', 60)->comment('importacion,estadisticas,horarios,reportes,riesgo');
            $table->json('payload')->nullable();
            $table->json('resultado')->nullable();
            $table->enum('estado', ['pendiente','procesando','completado','fallido'])->default('pendiente');
            $table->string('archivo_resultado', 500)->nullable();
            $table->text('error')->nullable();
            $table->integer('progreso')->default(0)->comment('0-100');
            $table->timestamp('iniciado_at')->nullable();
            $table->timestamp('completado_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->index(['organizacion_id','estado']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('python_jobs');
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('reingresos');
        Schema::dropIfExists('mantenimiento');
    }
};
