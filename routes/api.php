<?php

use App\Settings\HistoricoSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/transmissao/status', function () {
    $transmissao = \App\Models\TransmissaoSetting::current();

    return response()->json([
        'status' => $transmissao->status,
        'status_label' => $transmissao->status_label,
        'titulo' => $transmissao->titulo_transmissao,
        'descricao' => $transmissao->descricao,
        'youtube_url' => $transmissao->youtube_url,
        'youtube_video_id' => $transmissao->youtube_video_id,
        'iniciada_em' => $transmissao->iniciada_em,
        'duracao' => $transmissao->duracao_transmissao,
    ]);
});


Route::prefix('noticias-painel')->group(function () {
    Route::get('/', function () {
        $noticias = \App\Models\Noticia::publicadas()
            ->with(['autorParlamentar', 'projetoRelacionado'])
            ->orderBy('breaking_news', 'desc')
            ->orderBy('ordem_destaque')
            ->orderBy('data_publicacao', 'desc')
            ->paginate(20);

        return response()->json($noticias);
    });

    // Rota simples por ID
    Route::get('/{id}', function ($id) {
        $noticia = \App\Models\Noticia::where('id', $id)
            ->where('status', 'publicado')
            ->with(['autorParlamentar', 'projetoRelacionado', 'eventoRelacionado'])
            ->firstOrFail();

        $noticia->incrementarVisualizacoes();

        return response()->json($noticia);
    })->where('id', '[0-9]+'); // Garantir que só aceita números

});


Route::prefix('noticias')->group(function () {
    Route::get('/', function () {
        // 1. Buscar notícias do seu painel administrativo
        $noticiasDoPainel = \App\Models\Noticia::publicadas()
            ->with(['autorParlamentar', 'projetoRelacionado'])
            ->orderBy('breaking_news', 'desc')
            ->orderBy('ordem_destaque')
            ->orderBy('data_publicacao', 'desc')
            ->get(); // Usamos get() para obter todos os resultados para mesclagem

        // 2. Buscar notícias do feed RSS da Câmara
        $response = Http::get('https://rioverde.go.leg.br/feed/');
        $noticiasExternas = [];

        if ($response->successful()) {
            $xml = simplexml_load_string($response->body());
            foreach ($xml->channel->item as $item) {
                // Extrai a primeira imagem do conteúdo HTML
                $content = (string)$item->children('content', true)->encoded;
                $doc = new DOMDocument();
                @$doc->loadHTML($content);
                $imgTags = $doc->getElementsByTagName('img');
                $imagem = $imgTags->length > 0 ? $imgTags->item(0)->getAttribute('src') : null;

                $noticiasExternas[] = [
                    'id' => (string)$item->guid,
                    'titulo' => (string)$item->title,
                    'resumo' => (string)$item->description,
                    'conteudo' => $content,
                    'imagem_destaque' => $imagem,
                    'data_publicacao' => Carbon::parse((string)$item->pubDate)->toDateTimeString(),
                    'fonte' => 'Câmara Municipal de Rio Verde',
                    'link_externo' => (string)$item->link,
                ];
            }
        }

        // 3. Mesclar as notícias
        $noticiasMescladas = collect($noticiasDoPainel)->map(function ($noticia) {
            // Adiciona um campo para diferenciar a origem e garantir a consistência
            $noticia->fonte = 'Painel Administrativo';
            return $noticia;
        })->concat($noticiasExternas);

        // 4. Ordenar todas as notícias por data de publicação em ordem decrescente
        $noticiasOrdenadas = $noticiasMescladas->sortByDesc('data_publicacao')->values();

        // 5. Retornar o resultado paginado
        $porPagina = 20;
        $paginaAtual = request()->get('page', 1);
        $itensPaginados = $noticiasOrdenadas->slice(($paginaAtual - 1) * $porPagina, $porPagina)->values();
        $paginador = new \Illuminate\Pagination\LengthAwarePaginator(
            $itensPaginados,
            $noticiasOrdenadas->count(),
            $porPagina,
            $paginaAtual,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        return response()->json($paginador);
    });


    // Rota simples por ID
    Route::get('/{id}', function ($id) {
        $noticia = \App\Models\Noticia::where('id', $id)
            ->where('status', 'publicado')
            ->with(['autorParlamentar', 'projetoRelacionado', 'eventoRelacionado'])
            ->firstOrFail();

        $noticia->incrementarVisualizacoes();

        return response()->json($noticia);
    })->where('id', '[0-9]+'); // Garantir que só aceita números

});


Route::prefix('parlamentares')->group(function () {

    // Rota principal: listar parlamentares
    Route::get('/', function () {
        $parlamentares = \App\Models\Parlamentar::where('ativo_app', true)
            ->with(['partido', 'projetosAutor', 'projetosCoautor'])
            ->orderBy('ordem_exibicao', 'asc')
            ->paginate(20);

        return response()->json($parlamentares);
    });

    // Rota simples por ID
    Route::get('/{id}', function ($id) {
        $parlamentar = \App\Models\Parlamentar::where('id', $id)
            ->where('ativo_app', true)
            ->with(['partido', 'projetosAutor', 'projetosCoautor'])
            ->firstOrFail();

        return response()->json($parlamentar);
    })->where('id', '[0-9]+'); // Garantir que só aceita números

});


Route::prefix('midias')->group(function () {
    Route::get('/', function () {
        $midias = \App\Models\Midia::ativas()
            ->disponiveisApp()
            ->with(['eventoRelacionado'])
            ->orderBy('data_evento', 'desc') // 2. Ordena pela data mais recente
            ->paginate(15); // 3. Limita a 15 itens por página

        return response()->json($midias);
    });
});


Route::prefix('ouvidoria')->group(function () {
    // Enviar nova solicitação
    Route::post('/solicitacoes', function (Request $request) {
        $solicitacao = \App\Models\Solicitacao::create([
            'tipo' => $request->tipo,
            'categoria' => $request->categoria,
            'assunto' => $request->assunto,
            'descricao' => $request->descricao,
            'nome_cidadao' => $request->nome_cidadao,
            'email_cidadao' => $request->email_cidadao,
            'telefone_cidadao' => $request->telefone_cidadao,
            'endereco_cidadao' => $request->endereco_cidadao,
            'bairro' => $request->bairro,
            'localizacao' => $request->localizacao,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'identificacao_publica' => $request->identificacao_publica ?? false,
            'origem' => 'app',
            'ip_origem' => $request->ip(),
        ]);

        return response()->json([
            'protocolo' => $solicitacao->protocolo,
            'message' => 'Solicitação enviada com sucesso!'
        ]);
    });

    // Consultar solicitação por protocolo
    Route::get('/consultar/{protocolo}', function ($protocolo) {
        $solicitacao = \App\Models\Solicitacao::where('protocolo', $protocolo)->firstOrFail();

        return response()->json($solicitacao);
    });

    // Listar solicitações públicas
    Route::get('/publicas', function () {
        $solicitacoes = \App\Models\Solicitacao::publicas()
            ->with(['parlamentarResponsavel'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($solicitacoes);
    });
});



Route::prefix('eventos')->group(function () {

    // Listar todos os eventos (para popular o table_calendar)
    Route::get('/', function (Request $request) {
        $eventos = \App\Models\Evento::publicos()
            ->with(['projetoRelacionado'])
            ->orderBy('data', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get()
            ->map(function ($evento) {
                return [
                    'id' => $evento->id,
                    'titulo' => $evento->titulo,
                    'descricao' => $evento->descricao,
                    'data' => $evento->data->format('Y-m-d'),
                    'hora_inicio' => $evento->hora_inicio ? $evento->hora_inicio->format('H:i') : null,
                    'hora_fim' => $evento->hora_fim ? $evento->hora_fim->format('H:i') : null,
                    'dia_todo' => $evento->dia_todo,
                    'tipo' => $evento->tipo,
                    'tipo_label' => $evento->tipo_label,
                    'local' => $evento->local,
                    'cor_evento' => $evento->cor_evento,
                    'status' => $evento->status,
                    'destaque' => $evento->destaque,
                ];
            });

        return response()->json($eventos);
    });

    // Buscar evento específico por ID (para ver detalhes)
    Route::get('/{id}', function ($id) {
        $evento = \App\Models\Evento::where('id', $id)
            ->publicos()
            ->with(['projetoRelacionado'])
            ->firstOrFail();

        // Adicionar parlamentares relacionados
        $evento->append(['parlamentares']);

        return response()->json([
            'id' => $evento->id,
            'titulo' => $evento->titulo,
            'descricao' => $evento->descricao,
            'detalhes' => $evento->detalhes,
            'data' => $evento->data->format('Y-m-d'),
            'data_formatada' => $evento->data_formatada,
            'hora_inicio' => $evento->hora_inicio ? $evento->hora_inicio->format('H:i') : null,
            'hora_fim' => $evento->hora_fim ? $evento->hora_fim->format('H:i') : null,
            'horario_formatado' => $evento->horario_formatado,
            'dia_todo' => $evento->dia_todo,
            'tipo' => $evento->tipo,
            'tipo_label' => $evento->tipo_label,
            'local' => $evento->local,
            'endereco' => $evento->endereco,
            'cor_evento' => $evento->cor_evento,
            'status' => $evento->status,
            'status_label' => $evento->status_label,
            'publico' => $evento->publico,
            'transmissao_online' => $evento->transmissao_online,
            'link_transmissao' => $evento->link_transmissao,
            'observacoes' => $evento->observacoes,
            'anexos' => $evento->anexos,
            'destaque' => $evento->destaque,
            'parlamentares' => $evento->parlamentares,
            'projeto_relacionado' => $evento->projetoRelacionado,
            'created_at' => $evento->created_at,
            'updated_at' => $evento->updated_at,
        ]);
    })->where('id', '[0-9]+');
});

Route::prefix('historia')->group(function () {
    Route::get('/camara', function (Request $request) {
        $settings = app(HistoricoSettings::class);

        return response()->json([
            'titulo' => $settings->camara_titulo,
            'subtitulo' => $settings->camara_subtitulo,
            'conteudo' => $settings->camara_conteudo,
            'imagem_destaque' => $settings->camara_imagem_destaque,
            'galeria_imagens' => $settings->camara_galeria_imagens,
        ]);
    });
    Route::get('/cidade', function (Request $request) {
        $settings = app(HistoricoSettings::class);

        return response()->json([
            'titulo' => $settings->cidade_titulo,
            'subtitulo' => $settings->cidade_subtitulo,
            'conteudo' => $settings->cidade_conteudo,
            'imagem_destaque' => $settings->cidade_imagem_destaque,
            'galeria_imagens' => $settings->cidade_galeria_imagens,
        ]);
    });
});



Route::prefix('pontos-interesse')->group(function () {
    /**
     * Rota principal para listar e filtrar os pontos de interesse.
     *
     * Exemplos de uso no App:
     * - Listar todos: GET /api/pontos-interesse
     * - Filtrar por categoria: GET /api/pontos-interesse?categoria=turismo
     * - Filtrar por características: GET /api/pontos-interesse?caracteristicas=wifi_publico,acessibilidade
     * - Combinar filtros: GET /api/pontos-interesse?categoria=lazer_esporte&caracteristicas=estacionamento
     */
    Route::get('/', function (Request $request) {
        $query = \App\Models\PontoInteresse::query()->where('status', 'ativo');

        // Filtra por categoria, se o parâmetro for enviado
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // Filtra por características, se o parâmetro for enviado
        if ($request->filled('caracteristicas')) {
            $caracteristicas = explode(',', $request->caracteristicas);
            foreach ($caracteristicas as $caracteristica) {
                $coluna = trim($caracteristica);
                // Garante que só filtremos por colunas booleanas válidas
                if (in_array($coluna, ['acessibilidade', 'estacionamento', 'wifi_publico'])) {
                    $query->where($coluna, true);
                }
            }
        }

        $pontos = $query->orderBy('ordem_exibicao', 'asc')
            ->orderBy('nome', 'asc')
            ->select([ // Seleciona apenas os campos importantes para a listagem no app
                'id',
                'nome',
                'categoria',
                'subcategoria',
                'foto_principal',
                'latitude',
                'longitude',
                'horario_funcionamento'
            ])
            ->paginate(20);

        return response()->json($pontos);
    });

    /**
     * Rota para buscar os filtros disponíveis (categorias e características).
     * O app pode usar esta rota para montar a tela de filtros dinamicamente.
     */
    Route::get('/filtros', function () {
        return response()->json([
            'categorias' => [
                ['value' => 'educacao', 'label' => 'Educação'],
                ['value' => 'saude', 'label' => 'Saúde'],
                ['value' => 'lazer_esporte', 'label' => 'Lazer e Esporte'],
                ['value' => 'servicos_publicos', 'label' => 'Serviços Públicos'],
                ['value' => 'transporte', 'label' => 'Transporte'],
                ['value' => 'seguranca', 'label' => 'Segurança'],
                ['value' => 'cultura', 'label' => 'Cultura'],
                ['value' => 'assistencia_social', 'label' => 'Assistência Social'],
                ['value' => 'meio_ambiente', 'label' => 'Meio Ambiente'],
                ['value' => 'legislativo', 'label' => 'Legislativo'],
                ['value' => 'obras_andamento', 'label' => 'Obras em Andamento'],
                ['value' => 'locais_votacao', 'label' => 'Locais de Votação'],
                ['value' => 'turismo', 'label' => 'Turismo'],
                ['value' => 'religioso', 'label' => 'Religioso'],
                ['value' => 'comercio_servicos', 'label' => 'Comércio e Serviços'],
            ],
            'caracteristicas' => [
                ['value' => 'acessibilidade', 'label' => 'Acessibilidade'],
                ['value' => 'wifi_publico', 'label' => 'Wi-Fi Público'],
                ['value' => 'estacionamento', 'label' => 'Estacionamento'],
            ]
        ]);
    });

    /**
     * Rota para buscar um ponto de interesse específico por ID.
     * Retorna todos os detalhes para a tela de visualização do ponto.
     */
    Route::get('/{id}', function ($id) {
        $ponto = \App\Models\PontoInteresse::where('id', $id)
            ->where('status', 'ativo')
            ->firstOrFail();

        return response()->json($ponto);
    })->where('id', '[0-9]+');
});

