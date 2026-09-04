<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizacion_id')->nullable()
                  ->constrained('organizaciones')->nullOnDelete()
                  ->comment('null = rol global del sistema');
            $table->string('nombre', 60)->unique();
            $table->string('slug', 60)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('es_sistema')->default(false)
                  ->comment('true = no puede modificarse ni eliminarse');
            $table->integer('nivel')->default(99)
                  ->comment('1=superadmin ... 99=mínimo. Controla jerarquía.');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['organizacion_id', 'activo']);
        });

        // Permisos
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique()
                  ->comment('Ej: alumnos.crear, calificaciones.registrar');
            $table->string('slug', 100)->unique();
            $table->string('modulo', 60)
                  ->comment('Ej: alumnos, calificaciones, horarios, pagos');
            $table->string('accion', 60)
                  ->comment('Ej: ver, crear, editar, eliminar, autorizar');
            $table->string('descripcion', 255)->nullable();
            $table->string('alcance_default')->default('propio')
                  ->comment('global, organizacion, escuela, sede, ciclo, grupo, propio');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['modulo', 'accion']);
        });

        // Pivot: rol → permisos (con alcance)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->string('alcance')->default('propio')
                  ->comment('global, organizacion, escuela, sede, ciclo, grupo, propio');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
            $table->index('role_id');
            $table->index('permission_id');
        });

        // Pivot: usuario → roles (con scope de sede/escuela opcional)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete()
                  ->comment('null = aplica globalmente en la organización');
            $table->foreignId('escuela_id')->nullable()->constrained('escuelas')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamp('valido_desde')->nullable();
            $table->timestamp('valido_hasta')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'role_id', 'sede_id'], 'user_role_sede_unique');
            $table->index(['user_id', 'activo']);
        });

        // Permisos directos por usuario (overrides puntuales)
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->string('alcance')->default('propio');
            $table->boolean('concedido')->default(true)
                  ->comment('true=concedido, false=denegado explícito');
            $table->timestamps();

            $table->unique(['user_id', 'permission_id', 'sede_id'], 'user_perm_sede_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
