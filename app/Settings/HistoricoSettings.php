<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HistoricoSettings extends Settings
{
    // História da Câmara
    public string $camara_titulo;
    public ?string $camara_subtitulo;
    public string $camara_conteudo;
    public ?string $camara_imagem_destaque;
    public ?array $camara_galeria_imagens;
    public ?array $camara_meta_dados;

    // História da Cidade
    public string $cidade_titulo;
    public ?string $cidade_subtitulo;
    public string $cidade_conteudo;
    public ?string $cidade_imagem_destaque;
    public ?array $cidade_galeria_imagens;
    public ?array $cidade_meta_dados;

    public static function group(): string
    {
        return 'historico';
    }
}
