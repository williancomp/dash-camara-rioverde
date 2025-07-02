<?php

namespace App\Filament\Resources\Noticias\Pages;

use App\Filament\Resources\Noticias\NoticiaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Forms;

class EditNoticia extends EditRecord
{
    protected static string $resource = NoticiaResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        $actions = [
            DeleteAction::make(),
        ];

        // Ações baseadas no status atual
        if ($record->status === 'rascunho') {
            $actions[] = Action::make('publicar_agora')
                ->label('Publicar Agora')
                ->color('success')
                ->icon('heroicon-o-megaphone')
                ->requiresConfirmation()
                ->modalHeading('Publicar Notícia')
                ->modalDescription('A notícia ficará imediatamente visível no aplicativo.')
                ->action(function () {
                    $data = $this->form->getState();
                    $data['status'] = 'publicado';
                    $data['data_publicacao'] = now();

                    // Garantir que campos JSON sejam arrays ou null
                    $data['galeria_fotos'] = $data['galeria_fotos'] ?? [];
                    $data['tags'] = $data['tags'] ?? [];
                    $data['parlamentares_relacionados'] = $data['parlamentares_relacionados'] ?? [];

                    $this->handleRecordUpdate($this->getRecord(), $data);

                    Notification::make()
                        ->title('Notícia publicada com sucesso!')
                        ->success()
                        ->send();
                });

            $actions[] = Action::make('agendar')
                ->label('Agendar Publicação')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->schema([
                    DateTimePicker::make('data_agendamento')
                        ->label('Agendar para')
                        ->required()
                        ->minDate(now())
                        ->default(now()->addHour()),
                ])
                ->action(function (array $data) {
                    $formData = $this->form->getState();
                    $formData['status'] = 'agendado';
                    $formData['data_agendamento'] = $data['data_agendamento'];

                    // Garantir que campos JSON sejam arrays ou null
                    $formData['galeria_fotos'] = $formData['galeria_fotos'] ?? [];
                    $formData['tags'] = $formData['tags'] ?? [];
                    $formData['parlamentares_relacionados'] = $formData['parlamentares_relacionados'] ?? [];

                    $this->handleRecordUpdate($this->getRecord(), $formData);

                    Notification::make()
                        ->title('Notícia agendada para ' . Carbon::parse($data['data_agendamento'])->format('d/m/Y H:i'))
                        ->success()
                        ->send();
                });
        }

        if ($record->status === 'publicado') {
            $actions[] = Action::make('despublicar')
                ->label('Voltar p/ Rascunho')
                ->color('gray')
                ->icon('heroicon-o-document-text')
                ->requiresConfirmation()
                ->modalHeading('Voltar para Rascunho')
                ->modalDescription('A notícia será removida do aplicativo e voltará para edição.')
                ->action(function () {
                    $updateData = $this->data;
                    $updateData['status'] = 'rascunho';
                    $updateData['data_publicacao'] = null;
                    $this->handleRecordUpdate($this->getRecord(), $updateData);

                    Notification::make()
                        ->title('Notícia movida para rascunho!')
                        ->success()
                        ->send();
                });

            $actions[] = Action::make('arquivar')
                ->label('Arquivar')
                ->color('warning')
                ->icon('heroicon-o-archive-box')
                ->requiresConfirmation()
                ->modalHeading('Arquivar Notícia')
                ->modalDescription('A notícia será removida do aplicativo mas mantida no sistema.')
                ->action(function () {
                    $updateData = $this->data;
                    $updateData['status'] = 'arquivado';
                    $this->handleRecordUpdate($this->getRecord(), $updateData);

                    Notification::make()
                        ->title('Notícia arquivada!')
                        ->success()
                        ->send();
                });
        }

        if ($record->status === 'agendado') {
            $actions[] = Action::make('publicar_agora')
                ->label('Publicar Agora')
                ->color('success')
                ->icon('heroicon-o-megaphone')
                ->requiresConfirmation()
                ->action(function () {
                    $updateData = $this->data;
                    $updateData['status'] = 'publicado';
                    $updateData['data_publicacao'] = now();
                    $updateData['data_agendamento'] = null;
                    $this->handleRecordUpdate($this->getRecord(), $updateData);

                    Notification::make()
                        ->title('Notícia publicada imediatamente!')
                        ->success()
                        ->send();
                });

            $actions[] = Action::make('cancelar_agendamento')
                ->label('Cancelar Agendamento')
                ->color('gray')
                ->action(function () {
                    $updateData = $this->data;
                    $updateData['status'] = 'rascunho';
                    $updateData['data_agendamento'] = null;
                    $this->handleRecordUpdate($this->getRecord(), $updateData);

                    Notification::make()
                        ->title('Agendamento cancelado!')
                        ->success()
                        ->send();
                });
        }

        if ($record->status === 'arquivado') {
            $actions[] = Action::make('desarquivar')
                ->label('📤 Desarquivar')
                ->color('info')
                ->action(function () {
                    $updateData = $this->data;
                    $updateData['status'] = 'rascunho';
                    $this->handleRecordUpdate($this->getRecord(), $updateData);

                    Notification::make()
                        ->title('Notícia desarquivada!')
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('💾 Salvar Alterações');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['editor_nome'] = auth()->user()?->name;
        $data['editor_email'] = auth()->user()?->email;

        // Garantir que campos JSON sejam arrays válidos
        $data['galeria_fotos'] = $data['galeria_fotos'] ?? [];
        $data['tags'] = $data['tags'] ?? [];
        $data['parlamentares_relacionados'] = $data['parlamentares_relacionados'] ?? [];

        return $data;
    }
}
