<?php

namespace App\Livewire;

use App\Models\PontoInteresse;
use Livewire\Component;

class MapaInterativo extends Component
{
    public $pontos = [];
    public $mapId;

    protected $listeners = ['atualizarMapa'];

    public function mount($pontos = [])
    {
        $this->pontos = $pontos;
        $this->mapId = 'mapa' . str_replace(['-', '_'], '', uniqid());
    }

    // NOVO MÉTODO CHAMADO PELO WIRE:INIT
    public function carregarDadosIniciais()
    {
        // Despacha os pontos iniciais para o front-end
        $this->dispatch('pontosAtualizados', pontos: $this->pontos);
    }



    public function atualizarMapa($novosPontos)
    {
        $this->pontos = $novosPontos;
        $this->dispatch('pontosAtualizados', pontos: $this->pontos);
    }

    public function render()
    {
        return view('livewire.mapa-interativo');
    }
}
