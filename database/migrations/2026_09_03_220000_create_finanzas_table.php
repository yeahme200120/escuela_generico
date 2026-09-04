<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Conceptos de pago ─────────────────────────────────────────
        Schema::create('conceptos_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('nombre', 200);
            $table->string('clave', 50)->nullable();
            $table->enum('tipo', [
                'inscripcion', 'reinscripcion', 'colegiatura', 'transporte',
                'seguro', 'uniformes', 'libros', 'talleres', 'examenes',
                'certificados', 'constancias', 'otro',
            ])->default('otro');
            $table->decimal('importe_default', 12, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['organizacion_id', 'activo']);
        });

        // ── Cargos ────────────────────────────────────────────────────
        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('ciclo_escolar_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('concepto_id')->constrained('conceptos_pago')->restrictOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->string('referencia', 100)->nullable();
            $table->decimal('importe', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('recargo', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'parcial', 'pagado', 'cancelado', 'vencido'])->default('pendiente');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['alumno_id', 'estado']);
            $table->index(['sede_id', 'estado']);
        });

        // ── Parcialidades ─────────────────────────────────────────────
        Schema::create('parcialidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_id')->constrained('cargos')->cascadeOnDelete();
            $table->tinyInteger('numero');
            $table->date('fecha_vencimiento');
            $table->decimal('importe', 12, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'vencido'])->default('pendiente');
            $table->timestamps();

            $table->index(['cargo_id']);
        });

        // ── Métodos de pago ───────────────────────────────────────────
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('clave', 30)->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // ── Pagos ─────────────────────────────────────────────────────
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->unsignedBigInteger('caja_id')->nullable();
            $table->string('referencia', 100)->nullable();
            $table->decimal('importe', 12, 2);
            $table->date('fecha_pago');
            $table->foreignId('metodo_pago_id')->nullable()->constrained('metodos_pago')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['activo', 'cancelado', 'devuelto'])->default('activo');
            $table->string('motivo_cancelacion', 300)->nullable();
            $table->foreignId('cancelado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelado_at')->nullable();
            $table->string('idempotency_key', 100)->nullable()->unique();
            $table->string('request_id', 36)->nullable();
            $table->timestamps();

            $table->index(['alumno_id', 'estado']);
            $table->index(['sede_id', 'fecha_pago']);
        });

        // ── Detalle de pagos ──────────────────────────────────────────
        Schema::create('pago_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->cascadeOnDelete();
            $table->foreignId('cargo_id')->constrained('cargos')->cascadeOnDelete();
            $table->foreignId('parcialidad_id')->nullable()->constrained('parcialidades')->nullOnDelete();
            $table->decimal('importe_aplicado', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_detalle');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('metodos_pago');
        Schema::dropIfExists('parcialidades');
        Schema::dropIfExists('cargos');
        Schema::dropIfExists('conceptos_pago');
    }
};
