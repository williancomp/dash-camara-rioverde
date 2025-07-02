<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->string('numero'); // PL 001/2024
            $table->enum('tipo', ['projeto_lei', 'requerimento', 'indicacao', 'mocao']);
            $table->string('titulo');
            $table->text('ementa');
            $table->longText('texto_completo')->nullable();

            // Relacionamento com autor principal
            $table->foreignId('autor_id')->constrained('parlamentares')->onDelete('cascade');

            $table->date('data_apresentacao');
            $table->enum('status', ['apresentado', 'tramitando', 'aprovado', 'rejeitado', 'arquivado'])->default('apresentado');
            $table->enum('categoria', ['saude', 'educacao', 'transporte', 'meio_ambiente', 'seguranca', 'economia', 'social', 'infraestrutura', 'cultura', 'esporte', 'outros'])->default('outros');
            $table->enum('prioridade', ['baixa', 'media', 'alta'])->default('media');
            $table->boolean('destaque_app')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
