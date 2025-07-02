<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitacoes', function (Blueprint $table) {
            $table->id();

            // Identificação e protocolo
            $table->string('protocolo')->unique(); // Ex: SOL-2025-000001
            $table->enum('tipo', [
                'sugestao',
                'reclamacao',
                'denuncia',
                'elogio',
                'pedido_informacao',
                'solicitacao_servico',
                'proposta_projeto',
                'outros'
            ]);

            // Dados do cidadão
            $table->string('nome_cidadao');
            $table->string('email_cidadao');
            $table->string('telefone_cidadao')->nullable();
            $table->string('cpf_cidadao')->nullable();
            $table->text('endereco_cidadao')->nullable();
            $table->string('bairro')->nullable();
            $table->boolean('identificacao_publica')->default(false); // Se pode divulgar o nome

            // Conteúdo da solicitação
            $table->string('assunto');
            $table->text('descricao');
            $table->json('anexos')->nullable(); // Array de arquivos/fotos
            $table->string('localizacao')->nullable(); // Endereço do problema
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Categorização
            $table->enum('categoria', [
                'infraestrutura',
                'saude',
                'educacao',
                'seguranca',
                'meio_ambiente',
                'transporte',
                'assistencia_social',
                'cultura_esporte',
                'administracao',
                'fiscalizacao',
                'outros'
            ]);
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->json('tags')->nullable();

            // Atribuição e responsável
            $table->foreignId('parlamentar_responsavel_id')->nullable()->constrained('parlamentares')->onDelete('set null');
            $table->string('setor_responsavel')->nullable(); // Ex: "Obras", "Saúde"
            $table->foreignId('atribuido_por_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('data_atribuicao')->nullable();

            // Status e acompanhamento
            $table->enum('status', [
                'recebida',
                'em_analise',
                'em_andamento',
                'aguardando_informacoes',
                'resolvida',
                'rejeitada',
                'arquivada'
            ])->default('recebida');
            $table->text('justificativa_status')->nullable();
            $table->timestamp('prazo_resposta')->nullable();
            $table->timestamp('data_resolucao')->nullable();

            // Relacionamentos
            $table->foreignId('projeto_relacionado_id')->nullable()->constrained('projetos')->onDelete('set null');
            $table->string('numero_processo')->nullable(); // Se virou processo oficial

            // Configurações de visibilidade
            $table->boolean('publica')->default(false); // Se aparece no portal transparência
            $table->boolean('destaque')->default(false);
            $table->boolean('notificar_cidadao')->default(true);

            // Estatísticas e avaliação
            $table->integer('visualizacoes')->default(0);
            $table->integer('apoios')->default(0); // Quantos cidadãos apoiam
            $table->decimal('avaliacao_cidadao', 2, 1)->nullable(); // Nota de 1 a 5
            $table->text('comentario_avaliacao')->nullable();

            // Metadados
            $table->string('origem')->default('app'); // app, site, presencial, telefone
            $table->string('ip_origem')->nullable();
            $table->json('dados_extras')->nullable(); // Campos extras flexíveis

            $table->timestamps();

            // Índices
            $table->index(['status', 'created_at']);
            $table->index(['tipo', 'categoria']);
            $table->index(['parlamentar_responsavel_id', 'status']);
            $table->index(['protocolo']);
            $table->index(['email_cidadao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitacoes');
    }
};
