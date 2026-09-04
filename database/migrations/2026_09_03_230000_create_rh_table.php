<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->string('numero_empleado', 30)->nullable()->unique();
            $table->string('puesto', 150)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->enum('tipo_contrato', ['base','contrato','honorarios','tiempo_parcial'])->default('contrato');
            $table->decimal('salario', 12, 2)->nullable();
            $table->enum('estatus', ['activo','baja','suspendido'])->default('activo');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organizacion_id','estatus']);
        });

        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->string('folio', 50)->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('tipo', ['base','contrato','honorarios','tiempo_parcial'])->default('contrato');
            $table->decimal('salario', 12, 2)->nullable();
            $table->string('documento', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['empleado_id','activo']);
        });

        Schema::create('asistencia_personal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->date('fecha');
            $table->enum('estado', ['presente','falta','retardo','justificada','vacaciones'])->default('presente');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->decimal('precision_metros', 10, 2)->nullable();
            $table->string('fuente_ubicacion', 30)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['empleado_id','fecha']);
            $table->index(['sede_id','fecha']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('asistencia_personal');
        Schema::dropIfExists('contratos');
        Schema::dropIfExists('empleados');
    }
};
