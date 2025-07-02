<?php

namespace App\Filament\Widgets;

use App\Models\TransmissaoSetting;
use Filament\Widgets\Widget;
use Livewire\Component;

class TransmissaoControlWidget extends Widget
{
    protected string $view = 'filament.widgets.transmissao-control-widget';



    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public $showIniciarForm = false;
    public $showEditarForm = false;

    // Propriedades do formulário
    public $titulo_transmissao = '';
    public $descricao = '';
    public $youtube_url = '';
    public $notificar_usuarios = true;

    public function mount()
    {
        $transmissao = TransmissaoSetting::current();
        $this->titulo_transmissao = $transmissao->titulo_transmissao ?? 'Transmissão da Câmara Municipal';
        $this->descricao = $transmissao->descricao ?? '';
        $this->youtube_url = $transmissao->youtube_url ?? '';
    }

    public function getTransmissao()
    {
        return TransmissaoSetting::current();
    }

    // Força o widget a ocupar a linha toda
    public function getColumnSpan(): string | array | int
    {
        return 'full';
    }

    public function abrirFormIniciar()
    {
        $this->showIniciarForm = true;
        $this->showEditarForm = false;
    }

    public function abrirFormEditar()
    {
        $transmissao = TransmissaoSetting::current();
        $this->titulo_transmissao = $transmissao->titulo_transmissao;
        $this->descricao = $transmissao->descricao;
        $this->youtube_url = $transmissao->youtube_url;
        $this->showEditarForm = true;
        $this->showIniciarForm = false;
    }

    public function fecharForms()
    {
        $this->showIniciarForm = false;
        $this->showEditarForm = false;
        $this->resetErrorBag();
    }

    public function iniciarTransmissao()
    {
        $this->validate([
            'titulo_transmissao' => 'required|string|max:255',
            'youtube_url' => 'required|url',
        ]);

        $transmissao = TransmissaoSetting::current();
        $transmissao->update([
            'status' => 'online',
            'titulo_transmissao' => $this->titulo_transmissao,
            'descricao' => $this->descricao,
            'youtube_url' => $this->youtube_url,
            'notificar_usuarios' => $this->notificar_usuarios,
            'iniciada_em' => now(),
            'finalizada_em' => null,
        ]);

        $this->showIniciarForm = false;
        $this->dispatch('transmissao-iniciada');

        // Notification equivalente
        session()->flash('message', 'Transmissão iniciada com sucesso!');
    }

    public function colocarAguarde()
    {
        $transmissao = TransmissaoSetting::current();
        $transmissao->update(['status' => 'aguarde']);

        session()->flash('message', 'Transmissão colocada em aguarde');
    }

    public function finalizarTransmissao()
    {
        $transmissao = TransmissaoSetting::current();
        $transmissao->update([
            'status' => 'offline',
            'finalizada_em' => now(),
        ]);

        session()->flash('message', 'Transmissão finalizada!');
    }

    public function editarTransmissao()
    {
        $this->validate([
            'titulo_transmissao' => 'required|string|max:255',
            'youtube_url' => 'required|url',
        ]);

        $transmissao = TransmissaoSetting::current();
        $transmissao->update([
            'titulo_transmissao' => $this->titulo_transmissao,
            'descricao' => $this->descricao,
            'youtube_url' => $this->youtube_url,
        ]);

        $this->showEditarForm = false;
        session()->flash('message', 'Detalhes da transmissão atualizados!');
    }

    public function tornarOnline()
    {
        // Pega a transmissão atual
        $transmissao = $this->getTransmissao();

        // Garante que a ação só seja executada se o status for 'aguarde'
        if ($transmissao && $transmissao->status === 'aguarde') {
            // Atualiza o status para 'online'
            $transmissao->update(['status' => 'online']);

            // Envia uma mensagem de sucesso para a interface
            session()->flash('message', 'A transmissão está agora AO VIVO!');
        }

        // Opcional, mas recomendado: Força a atualização do componente na tela
        // para que as mudanças apareçam imediatamente.
        $this->dispatch('$refresh');
    }
}
