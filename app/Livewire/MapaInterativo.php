<?php

namespace App\Livewire;

use App\Models\PontoInteresse;
use Livewire\Component;

class MapaInterativo extends Component
{
    public $pontos = [];
    public $mapId;

    // O listener que recebe os dados da página principal
    protected $listeners = ['atualizarMapa'];

    public function mount($pontos = [])
    {
        $this->pontos = $pontos;
        // Gera um ID único para o container do mapa
        $this->mapId = 'mapa' . uniqid();
    }

    // Este método é chamado pelo evento despachado de MapaVisualizador
    public function atualizarMapa($novosPontos)
    {
        $this->pontos = $novosPontos;

        // Emite um evento para o JavaScript com os novos pontos
        $this->dispatch('pontosAtualizados', pontos: $this->pontos);
    }

    public function render()
    {
        return view('livewire.mapa-interativo');
    }
}
