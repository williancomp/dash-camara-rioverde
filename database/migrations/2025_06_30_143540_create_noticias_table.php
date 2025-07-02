<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();

            // Dados básicos
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumo'); // Para listagens
            $table->longText('conteudo'); // Conteúdo completo
            $table->string('foto_capa')->nullable(); // Imagem principal
            $table->json('galeria_fotos')->nullable(); // Array de imagens adicionais

            // Categorização
            $table->enum('categoria', [
                'sessao',
                'projeto_lei',
                'audiencia_publica',
                'evento',
                'homenagem',
                'comunicado',
                'obra_publica',
                'saude',
                'educacao',
                'transporte',
                'meio_ambiente',
                'social',
                'economia',
                'cultura',
                'esporte',
                'geral'
            ])->default('geral');

            $table->json('tags')->nullable(); // Array de tags

            // Relacionamentos
            $table->foreignId('autor_parlamentar_id')->nullable()->constrained('parlamentares')->onDelete('set null');
            $table->json('parlamentares_relacionados')->nullable(); // Array de IDs
            $table->foreignId('projeto_relacionado_id')->nullable()->constrained('projetos')->onDelete('set null');
            $table->foreignId('evento_relacionado_id')->nullable()->constrained('eventos')->onDelete('set null');

            // Status e publicação
            $table->enum('status', ['rascunho', 'agendado', 'publicado', 'arquivado'])->default('rascunho');
            $table->timestamp('data_publicacao')->nullable();
            $table->timestamp('data_agendamento')->nullable();

            // SEO e configurações
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('fonte')->nullable(); // Ex: "Assessoria de Comunicação"

            // Configurações do app
            $table->boolean('destaque')->default(false); // Aparece em destaque
            $table->boolean('breaking_news')->default(false); // Notícia urgente
            $table->boolean('notificar_usuarios')->default(false);
            $table->integer('ordem_destaque')->default(0);
            $table->boolean('permitir_comentarios')->default(true);

            // Estatísticas
            $table->integer('visualizacoes')->default(0);
            $table->integer('curtidas')->default(0);
            $table->integer('compartilhamentos')->default(0);

            // Dados do editor
            $table->string('editor_nome')->nullable(); // Nome de quem editou
            $table->string('editor_email')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['status', 'data_publicacao']);
            $table->index(['categoria', 'destaque']);
            $table->index(['breaking_news', 'data_publicacao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
