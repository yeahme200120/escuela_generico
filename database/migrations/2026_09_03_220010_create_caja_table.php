<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Cajas ─────────────────────────────────────────────────────
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        // ── Turnos de caja ────────────────────────────────────────────
        Schema::create('turnos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->decimal('monto_apertura', 12, 2)->default(0);
            $table->decimal('monto_cierre', 12, 2)->nullable();
            $table->decimal('monto_esperado', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->enum('estado', ['abierto', 'cerrado', 'arqueo'])->default('abierto');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['caja_id', 'estado']);
            $table->index(['usuario_id']);
        });

        // ── Movimientos de caja ───────────────────────────────────────
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('turno_caja_id')->constrained('turnos_caja')->cascadeOnDelete();
            $table->enum('tipo', ['ingreso', 'egreso', 'retiro', 'devolucion', 'ajuste'])->default('ingreso');
            $table->string('concepto', 300);
            $table->decimal('importe', 12, 2);
            $table->string('referencia', 100)->nullable();
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            // Sin updated_at

            $table->index(['turno_caja_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
        Schema::dropIfExists('turnos_caja');
        Schema::dropIfExists('cajas');
    }
};
