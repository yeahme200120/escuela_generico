<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notificacion_usuario')) {
            Schema::create('notificacion_usuario', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('notificacion_id');
                $table->unsignedBigInteger('usuario_id');
                $table->boolean('leida')->default(false);
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('notificacion_id')->references('id')->on('notificaciones')->onDelete('cascade');
                $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion_usuario');
    }
};