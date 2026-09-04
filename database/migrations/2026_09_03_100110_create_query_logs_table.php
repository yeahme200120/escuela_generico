<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('query_logs', function (Blueprint $table) {
            $table->id();

            // Vinculación con el request y auditoría
            $table->string('request_id', 36)->nullable()->index()
                  ->comment('Mismo X-Request-ID del audit_log correspondiente');
            $table->string('audit_log_uuid', 36)->nullable()->index();
            $table->string('session_uuid', 36)->nullable()->index();

            // Actor
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();

            // Query
            $table->string('connection', 30)->default('mysql');
            $table->text('sql')
                  ->comment('SQL con bindings reemplazados');
            $table->text('sql_raw')->nullable()
                  ->comment('SQL con placeholders ? sin reemplazar');
            $table->json('bindings')->nullable()
                  ->comment('Array de bindings usados');
            $table->decimal('tiempo_ms', 10, 3)->nullable()
                  ->comment('Tiempo de ejecución en milisegundos');
            $table->integer('filas_afectadas')->nullable();
            $table->string('tipo', 20)->nullable()
                  ->comment('SELECT, INSERT, UPDATE, DELETE, BEGIN, COMMIT, ROLLBACK');
            $table->string('tabla_principal', 100)->nullable()
                  ->comment('Tabla principal detectada del SQL');

            // Contexto de ejecución
            $table->string('origen', 200)->nullable()
                  ->comment('Clase::metodo desde donde se disparó la query');
            $table->boolean('en_transaccion')->default(false);
            $table->boolean('es_lenta')->default(false)
                  ->comment('true si supera el umbral de slow_query_ms en config');

            // Geo y dispositivo (heredados del request)
            $table->string('ip_address', 45)->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->decimal('precision_metros', 10, 2)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['request_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['tipo', 'es_lenta']);
            $table->index('tabla_principal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('query_logs');
    }
};
