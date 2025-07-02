<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();

            // Dados básicos do evento
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->longText('detalhes')->nullable(); // Texto completo para quando clicar

            // Data e hora
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->boolean('dia_todo')->default(false);

            // Tipo e local
            $table->enum('tipo', [
                'sessao_ordinaria',
                'sessao_extraordinaria',
                'sessao_solene',
                'audiencia_publica',
                'reuniao_comissao',
                'evento_especial',
                'feriado',
                'recesso'
            ]);
            $table->string('local')->nullable();
            $table->string('endereco')->nullable();

            // Relacionamentos
            $table->json('parlamentares_envolvidos')->nullable(); // Array de IDs dos parlamentares
            $table->foreignId('projeto_relacionado_id')->nullable()->constrained('projetos')->onDelete('set null');

            // Configurações
            $table->boolean('publico')->default(true); // Se é aberto ao público
            $table->boolean('transmissao_online')->default(false);
            $table->string('link_transmissao')->nullable();
            $table->string('cor_evento', 7)->default('#3B82F6'); // Cor no calendário

            // Status
            $table->enum('status', ['agendado', 'em_andamento', 'finalizado', 'cancelado', 'adiado'])->default('agendado');
            $table->text('observacoes')->nullable();

            // Anexos
            $table->json('anexos')->nullable(); // Array de caminhos de arquivos

            // Configurações do app
            $table->boolean('destaque')->default(false);
            $table->boolean('notificar_usuarios')->default(false);
            $table->integer('ordem_exibicao')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
