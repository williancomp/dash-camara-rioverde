<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacao_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitacao_id')->constrained('solicitacoes')->onDelete('cascade');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nome_usuario'); // Para manter histórico
            $table->enum('tipo', ['resposta', 'observacao', 'mudanca_status', 'solicitacao_info']);
            $table->text('mensagem');
            $table->json('anexos')->nullable();
            $table->boolean('visivel_cidadao')->default(true);
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacao_respostas');
    }
};
