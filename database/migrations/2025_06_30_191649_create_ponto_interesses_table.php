<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pontos_interesse', function (Blueprint $table) {
            $table->id();

            // Dados básicos
            $table->string('nome');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable();
            $table->string('foto_principal')->nullable();
            $table->json('galeria_fotos')->nullable();

            // Localização
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('endereco_completo');
            $table->string('bairro');
            $table->string('cep', 10)->nullable();
            $table->string('referencia')->nullable(); // Ponto de referência

            // Categorização
            $table->enum('categoria', [
                'educacao',
                'saude',
                'lazer_esporte',
                'servicos_publicos',
                'transporte',
                'seguranca',
                'cultura',
                'assistencia_social',
                'meio_ambiente',
                'legislativo',
                'obras_andamento',
                'locais_votacao',
                'turismo',
                'religioso',
                'comercio_servicos'
            ]);

            $table->enum('subcategoria', [
                // Educação
                'escola_municipal',
                'escola_estadual',
                'escola_particular',
                'creche',
                'universidade',
                'biblioteca',

                // Saúde
                'ubs_posto_saude',
                'hospital',
                'clinica',
                'farmacia_popular',
                'consultorio',

                // Lazer e Esporte
                'praca',
                'parque',
                'quadra_esportiva',
                'ginasio',
                'campo_futebol',
                'pista_caminhada',
                'playground',

                // Serviços Públicos
                'prefeitura',
                'cartorio',
                'correios',
                'banco',
                'caixa_eletronico',
                'detran',
                'forum',
                'defensoria',

                // Transporte
                'ponto_onibus',
                'terminal_rodoviario',
                'taxi',
                'estacionamento',

                // Segurança
                'delegacia',
                'quartel_bombeiros',
                'guarda_municipal',
                'conselho_tutelar',

                // Cultura
                'centro_cultural',
                'museu',
                'teatro',
                'cinema',
                'casa_cultura',

                // Assistência Social
                'cras',
                'creas',
                'abrigo',
                'casa_idoso',

                // Meio Ambiente
                'coleta_seletiva',
                'area_preservacao',
                'nascente',
                'mata_ciliar',

                // Legislativo
                'camara_municipal',
                'gabinete_vereador',
                'plenario',

                // Obras
                'obra_pavimentacao',
                'obra_saneamento',
                'obra_construcao',
                'obra_reforma',

                // Votação
                'secao_eleitoral',
                'zona_eleitoral',

                // Turismo
                'ponto_turistico',
                'hotel_pousada',
                'restaurante',
                'artesanato',

                // Religioso
                'igreja',
                'templo',
                'centro_religioso',

                // Comércio
                'mercado',
                'farmacia',
                'posto_combustivel',
                'loja'
            ])->nullable();

            // Informações de contato
            $table->string('telefone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('site')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();

            // Horário de funcionamento
            $table->json('horario_funcionamento')->nullable(); // JSON com dias e horários
            $table->boolean('funciona_24h')->default(false);
            $table->text('observacoes_horario')->nullable();

            // Características e serviços
            $table->json('servicos_oferecidos')->nullable(); // Array de serviços
            $table->boolean('acessibilidade')->default(false);
            $table->boolean('estacionamento')->default(false);
            $table->boolean('wifi_publico')->default(false);
            $table->integer('capacidade')->nullable(); // Para eventos, escolas, etc.

            // Status e visibilidade
            $table->enum('status', ['ativo', 'inativo', 'em_obras', 'temporariamente_fechado'])->default('ativo');
            $table->boolean('destaque')->default(false);
            $table->boolean('verificado')->default(false); // Se foi verificado pela equipe
            $table->integer('ordem_exibicao')->default(0);

            // Dados administrativos
            $table->string('responsavel_cadastro')->nullable();
            $table->timestamp('data_verificacao')->nullable();
            $table->text('observacoes_internas')->nullable();

            // Estatísticas
            $table->integer('visualizacoes')->default(0);
            $table->integer('curtidas')->default(0);
            $table->decimal('avaliacao_media', 2, 1)->nullable(); // Média das avaliações
            $table->integer('total_avaliacoes')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            // Índices
            $table->index(['categoria', 'subcategoria']);
            $table->index(['status', 'destaque']);
            $table->index(['bairro', 'categoria']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pontos_interesse');
    }
};
