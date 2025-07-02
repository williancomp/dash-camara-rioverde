<?php

namespace App\Filament\Pages;

use App\Models\PontoInteresse;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class MapaVisualizador extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected string $view = 'filament.pages.mapa-visualizador';

    protected static ?string $title = 'Mapa Visualizador';

    protected static ?int $navigationSort = 9;

    protected static string|UnitEnum|null $navigationGroup = 'Mapa da Cidade';


    public static function getNavigationLabel(): string
    {
        return 'Visualizar Mapa';
    }

    public string $categoriaFiltro = 'todos';
    public $pontosInteresse = [];

    public function mount()
    {
        $this->carregarPontos();
    }

    public function carregarPontos()
    {
        $query = PontoInteresse::where('status', 'ativo');

        if ($this->categoriaFiltro !== 'todos') {
            $query->where('categoria', $this->categoriaFiltro);
        }

        $this->pontosInteresse = $query->orderBy('nome')->get();
    }

    public function filtrarPorCategoria($categoria)
    {
        $this->categoriaFiltro = $categoria;
        $this->carregarPontos();

        // Despacha o evento com os novos pontos para o componente 'mapa-interativo'
        $this->dispatch('atualizarMapa', novosPontos: $this->pontosInteresse);
    }

    public function getPontosParaMapa()
    {
        return response()->json($this->pontosInteresse);
    }

    public function getCategorias()
    {
        return [
            'todos' => 'Todas',
            'educacao' => 'Educação',
            'saude' => 'Saúde',
            'lazer_esporte' => 'Lazer',
            'servicos_publicos' => 'Serviços',
            'legislativo' => 'Legislativo',
            'turismo' => 'Turismo',
            'religioso' => 'Religioso',
            'comercio_servicos' => 'Comércio'
        ];
    }
}
