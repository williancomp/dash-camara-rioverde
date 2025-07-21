<?php

use App\Settings\HistoricoSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::prefix('noticias')->group(function () {
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
            ->orderBy('destaque', 'desc')
            ->orderBy('ordem_exibicao')
            ->orderBy('data_evento', 'desc')
            ->paginate(20);

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

Route::prefix('rota-teste')->group(function () {
    Route::get('/', function (Request $request) {
        return "Rota testada...., muito bem..";
    });
});
