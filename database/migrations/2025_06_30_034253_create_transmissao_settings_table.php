<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transmissao_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['online', 'offline', 'aguarde'])->default('offline');
            $table->string('youtube_url')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->string('titulo_transmissao')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('finalizada_em')->nullable();
            $table->boolean('notificar_usuarios')->default(false);
            $table->json('metadata')->nullable(); // Para dados extras
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transmissao_settings');
    }
};
