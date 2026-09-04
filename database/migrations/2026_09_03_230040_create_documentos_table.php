<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->string('nombre', 150);
            $table->string('clave', 50)->nullable();
            $table->enum('categoria', ['constancia','certificado','carta','justificante','kardex','boleta','acta','reconocimiento','otro'])->default('otro');
            $table->boolean('requiere_autorizacion')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento')->restrictOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->string('folio', 50)->nullable();
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('archivo', 255)->nullable();
            $table->string('hash_archivo', 64)->nullable();
            $table->enum('estado', ['pendiente','generado','autorizado','entregado','cancelado'])->default('pendiente');
            $table->foreignId('generado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('autorizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generado_at')->nullable();
            $table->timestamp('autorizado_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['alumno_id','estado']);
            $table->index(['sede_id','estado']);
        });

        Schema::create('folios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento')->cascadeOnDelete();
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->timestamps();
            $table->unique(['sede_id','tipo_documento_id','anio']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('folios');
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('tipos_documento');
    }
};
