<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique();
            $table->foreignId('organizacion_id')->nullable()->constrained('organizaciones')->nullOnDelete();
            $table->foreignId('remitente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo', 200);
            $table->text('cuerpo');
            $table->enum('canal', ['interna','email','sms','push'])->default('interna');
            $table->enum('tipo', ['info','exito','advertencia','peligro'])->default('info');
            $table->json('destinatarios_config')->nullable();
            $table->integer('total_enviados')->default(0);
            $table->integer('total_fallidos')->default(0);
            $table->enum('estado', ['borrador','enviando','enviado','fallido'])->default('borrador');
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();
            $table->index(['organizacion_id','estado']);
        });

        Schema::create('notificacion_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notificacion_id')->constrained('notificaciones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('leida')->default(false);
            $table->timestamp('leida_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['notificacion_id','user_id']);
            $table->index(['user_id','leida']);
        });

        Schema::create('calendario_escolar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->nullOnDelete();
            $table->foreignId('ciclo_escolar_id')->nullable()->constrained('ciclos_escolares')->nullOnDelete();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['inicio_clases','fin_clases','vacaciones','suspension','examen','consejo','evento','festivo'])->default('evento');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('todo_el_dia')->default(true);
            $table->string('color', 20)->nullable();
            $table->timestamps();
            $table->index(['sede_id','fecha_inicio']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('calendario_escolar');
        Schema::dropIfExists('notificacion_usuario');
        Schema::dropIfExists('notificaciones');
    }
};
