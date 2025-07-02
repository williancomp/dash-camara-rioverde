<?php

namespace App\Filament\Resources\Noticias\Pages;

use App\Filament\Resources\Noticias\NoticiaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateNoticia extends CreateRecord
{
    protected static string $resource = NoticiaResource::class;

    // Ações do cabeçalho (topo)
    protected function getHeaderActions(): array
    {
        return $this->getCustomActions();
    }

    // Ações do formulário (rodapé)
    protected function getFormActions(): array
    {
        return [
            ...$this->getCustomActions(),
            $this->getCancelAction(),
        ];
    }

    // Método centralizado para criar as ações customizadas
    private function getCustomActions(): array
    {
        return [
            $this->createSalvarRascunhoAction(),
            $this->createPublicarAgoraAction(),
        ];
    }

    // Ação: Salvar como Rascunho
    private function createSalvarRascunhoAction(): Action
    {
        return Action::make('salvar_rascunho')
            ->label('💾 Salvar como Rascunho')
            ->color('gray')
            ->action(function () {
                $data = $this->prepareDataForCreation('rascunho');
                $record = $this->handleRecordCreation($data);

                Notification::make()
                    ->title('Notícia salva como rascunho!')
                    ->success()
                    ->send();

                $this->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
            });
    }

    // Ação: Publicar Agora
    private function createPublicarAgoraAction(): Action
    {
        return Action::make('publicar_agora')
            ->label('📢 Salvar e Publicar')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Publicar Notícia')
            ->modalDescription('A notícia ficará imediatamente visível no aplicativo.')
            ->action(function () {
                $data = $this->prepareDataForCreation('publicado');
                $data['data_publicacao'] = now();
                $record = $this->handleRecordCreation($data);

                Notification::make()
                    ->title('Notícia publicada com sucesso!')
                    ->success()
                    ->send();

                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    // Ação: Cancelar
    private function getCancelAction(): Action
    {
        return Action::make('cancel')
            ->label('Cancelar')
            ->color('gray')
            ->url($this->getResource()::getUrl('index'));
    }

    // Método para preparar dados comuns
    private function prepareDataForCreation(string $status): array
    {
        $data = $this->form->getState();
        $data['status'] = $status;
        $data['editor_nome'] = auth()->user()?->name;
        $data['editor_email'] = auth()->user()?->email;

        // Garantir que campos JSON sejam arrays ou null
        $data['galeria_fotos'] = $data['galeria_fotos'] ?? [];
        $data['tags'] = $data['tags'] ?? [];
        $data['parlamentares_relacionados'] = $data['parlamentares_relacionados'] ?? [];

        return $data;
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['editor_nome'] = auth()->user()?->name;
        $data['editor_email'] = auth()->user()?->email;

        // Se não especificou status, mantém como rascunho
        if (!isset($data['status'])) {
            $data['status'] = 'rascunho';
        }

        // Garantir que campos JSON sejam arrays válidos
        $data['galeria_fotos'] = $data['galeria_fotos'] ?? [];
        $data['tags'] = $data['tags'] ?? [];
        $data['parlamentares_relacionados'] = $data['parlamentares_relacionados'] ?? [];

        // Garantir que campos numéricos tenham valores padrão
        $data['visualizacoes'] = 0;
        $data['curtidas'] = 0;
        $data['compartilhamentos'] = 0;
        $data['ordem_destaque'] = $data['ordem_destaque'] ?? 0;

        return $data;
    }



    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
