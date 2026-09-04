<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('python_job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('worker_type');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('estado')->nullable();
            $table->json('entrada')->nullable();
            $table->json('salida')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('python_job_logs');
    }
};