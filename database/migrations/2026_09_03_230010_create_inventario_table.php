<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->string('nombre', 100);
            $table->string('tipo', 30)->default('consumible');
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_inventario')->nullOnDelete();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 200);
            $table->string('descripcion', 400)->nullable();
            $table->string('unidad_medida', 30)->default('pieza');
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio_unitario', 12, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['sede_id','activo']);
        });

        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_id')->constrained('inventario')->cascadeOnDelete();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->enum('tipo', ['entrada','salida','transferencia','ajuste'])->default('entrada');
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_posterior');
            $table->string('referencia', 100)->nullable();
            $table->string('motivo', 300)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['inventario_id','tipo']);
        });

        Schema::create('activos_fijos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->constrained('sedes')->cascadeOnDelete();
            $table->foreignId('edificio_id')->nullable()->constrained('edificios')->nullOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->string('codigo', 50)->nullable()->unique();
            $table->string('nombre', 200);
            $table->string('categoria', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->decimal('valor', 12, 2)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->enum('estado', ['bueno','regular','malo','baja'])->default('bueno');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['sede_id','activo']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('activos_fijos');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('inventario');
        Schema::dropIfExists('categorias_inventario');
    }
};
