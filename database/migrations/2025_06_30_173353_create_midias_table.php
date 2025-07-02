<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midias', function (Blueprint $table) {
            $table->id();

            // Dados básicos
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->string('thumbnail')->nullable(); // Miniatura/capa do vídeo

            // Tipo de mídia
            $table->enum('tipo', [
                'sessao_ordinaria',
                'sessao_extraordinaria',
                'sessao_solene',
                'audiencia_publica',
                'reuniao_comissao',
                'evento_especial',
                'solenidade',
                'entrevista',
                'pronunciamento',
                'outros'
            ]);

            // Links e informações do vídeo
            $table->string('youtube_url')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('link_alternativo')->nullable(); // Para outros players
            $table->integer('duracao_segundos')->nullable(); // Duração em segundos
            $table->string('qualidade')->nullable(); // HD, 4K, etc.

            // Data e informações do evento
            $table->date('data_evento');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->string('local_evento')->nullable();

            // Relacionamentos
            $table->json('parlamentares_presentes')->nullable(); // Array de IDs
            $table->foreignId('evento_relacionado_id')->nullable()->constrained('eventos')->onDelete('set null');
            $table->json('projetos_discutidos')->nullable(); // Array de IDs de projetos

            // Categorização
            $table->json('tags')->nullable();
            $table->string('periodo_legislativo')->nullable(); // Ex: "2021-2024"
            $table->integer('ano')->nullable();
            $table->integer('mes')->nullable();

            // Status e configurações
            $table->enum('status', ['ativo', 'inativo', 'processando', 'erro'])->default('ativo');
            $table->boolean('destaque')->default(false);
            $table->integer('ordem_exibicao')->default(0);
            $table->boolean('disponivel_app')->default(true);

            // Estatísticas
            $table->integer('visualizacoes')->default(0);
            $table->integer('curtidas')->default(0);
            $table->integer('compartilhamentos')->default(0);

            // Informações adicionais
            $table->text('observacoes')->nullable();
            $table->string('responsavel_upload')->nullable(); // Quem fez o upload
            $table->timestamp('data_upload')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['status', 'data_evento']);
            $table->index(['tipo', 'ano', 'mes']);
            $table->index(['destaque', 'ordem_exibicao']);
            $table->index(['disponivel_app', 'data_evento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midias');
    }
};
