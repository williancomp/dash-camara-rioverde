<?php

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


// routes/api.php
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

    Route::get('/destaques', function () {
        $destaques = \App\Models\Noticia::publicadas()
            ->destaques()
            ->with(['autorParlamentar'])
            ->orderBy('ordem_destaque')
            ->orderBy('data_publicacao', 'desc')
            ->limit(5)
            ->get();

        return response()->json($destaques);
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


// routes/api.php
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

    Route::get('/destaques', function () {
        $destaques = \App\Models\Midia::ativas()
            ->disponiveisApp()
            ->destaques()
            ->orderBy('ordem_exibicao')
            ->orderBy('data_evento', 'desc')
            ->limit(10)
            ->get();

        return response()->json($destaques);
    });

    Route::get('/por-tipo/{tipo}', function ($tipo) {
        $midias = \App\Models\Midia::ativas()
            ->disponiveisApp()
            ->porTipo($tipo)
            ->orderBy('data_evento', 'desc')
            ->paginate(20);

        return response()->json($midias);
    });

    Route::get('/por-ano/{ano}', function ($ano) {
        $midias = \App\Models\Midia::ativas()
            ->disponiveisApp()
            ->porAno($ano)
            ->orderBy('data_evento', 'desc')
            ->paginate(20);

        return response()->json($midias);
    });

    Route::get('/{slug}', function ($slug) {
        $midia = \App\Models\Midia::where('slug', $slug)
            ->ativas()
            ->disponiveisApp()
            ->with(['eventoRelacionado'])
            ->firstOrFail();

        $midia->incrementarVisualizacoes();

        return response()->json($midia);
    });
});


// routes/api.php
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
