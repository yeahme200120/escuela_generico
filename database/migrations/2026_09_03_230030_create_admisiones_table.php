<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prospectos', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('sede_interes_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('nombres', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('nivel_interes', 100)->nullable();
            $table->string('grado_interes', 100)->nullable();
            $table->string('ciclo_interes', 30)->nullable();
            $table->enum('estatus', ['nuevo','contactado','citado','evaluado','admitido','rechazado','cancelado'])->default('nuevo');
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->index(['organizacion_id','estatus']);
        });

        Schema::create('seguimientos_prospecto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prospecto_id')->constrained('prospectos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', ['llamada','email','visita','cita','nota'])->default('nota');
            $table->text('descripcion');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('admisiones', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('prospecto_id')->nullable()->constrained('prospectos')->nullOnDelete();
            $table->foreignId('alumno_id')->nullable()->constrained('alumnos')->nullOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('grado_id')->nullable()->constrained('grados')->nullOnDelete();
            $table->enum('estatus', ['solicitada','documentos_pendientes','evaluacion','aprobada','rechazada','inscrito'])->default('solicitada');
            $table->date('fecha_solicitud');
            $table->date('fecha_resolucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('atendido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['sede_id','estatus']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('admisiones');
        Schema::dropIfExists('seguimientos_prospecto');
        Schema::dropIfExists('prospectos');
    }
};
