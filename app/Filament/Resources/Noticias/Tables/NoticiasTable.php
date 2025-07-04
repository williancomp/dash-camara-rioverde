<?php

namespace App\Filament\Resources\Noticias\Tables;

use App\Models\Noticia;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class NoticiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_capa')
                    ->label('Capa')
                    ->circular()
                    ->imageSize(50),

                TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                TextColumn::make('categoria')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sessao' => 'primary',
                        'projeto_lei' => 'success',
                        'audiencia_publica' => 'info',
                        'evento' => 'warning',
                        'comunicado' => 'danger',
                        'obra_publica' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(Noticia $record): string => $record->categoria_label),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'rascunho' => 'gray',
                        'agendado' => 'warning',
                        'publicado' => 'success',
                        'arquivado' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'rascunho' => 'heroicon-o-document-text',
                        'agendado' => 'heroicon-o-clock',
                        'publicado' => 'heroicon-o-eye',
                        'arquivado' => 'heroicon-o-archive-box',
                    })
                    ->formatStateUsing(fn(Noticia $record): string => match ($record->status) {
                        'rascunho' => $record->status_label,
                        'agendado' => $record->status_label,
                        'publicado' => $record->status_label,
                        'arquivado' => $record->status_label,
                    }),

                TextColumn::make('autorParlamentar.nome_parlamentar')
                    ->label('Autor')
                    ->toggleable(),

                IconColumn::make('destaque')
                    ->boolean()
                    ->label('Destaque'),

                IconColumn::make('breaking_news')
                    ->boolean()
                    ->label('Breaking'),

                TextColumn::make('visualizacoes')
                    ->label('Views')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('data_publicacao')
                    ->label('Publicação')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'rascunho' => 'Rascunho',
                        'agendado' => 'Agendado',
                        'publicado' => 'Publicado',
                        'arquivado' => 'Arquivado',
                    ]),

                SelectFilter::make('categoria')
                    ->options([
                        'sessao' => 'Sessão',
                        'projeto_lei' => 'Projeto de Lei',
                        'audiencia_publica' => 'Audiência Pública',
                        'evento' => 'Evento',
                        'homenagem' => 'Homenagem',
                        'comunicado' => 'Comunicado',
                        'obra_publica' => 'Obra Pública',
                        'saude' => 'Saúde',
                        'educacao' => 'Educação',
                        'transporte' => 'Transporte',
                        'meio_ambiente' => 'Meio Ambiente',
                        'social' => 'Social',
                        'economia' => 'Economia',
                        'cultura' => 'Cultura',
                        'esporte' => 'Esporte',
                        'geral' => 'Geral',
                    ]),

                TernaryFilter::make('destaque'),
                TernaryFilter::make('breaking_news'),

                Filter::make('data_publicacao')
                    ->schema([
                        DatePicker::make('publicado_de'),
                        DatePicker::make('publicado_ate'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when($data['publicado_de'], fn($q) => $q->whereDate('data_publicacao', '>=', $data['publicado_de']))
                            ->when($data['publicado_ate'], fn($q) => $q->whereDate('data_publicacao', '<=', $data['publicado_ate']));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // Separator
                    Action::make('separator1')
                        ->label('───── Publicação ──────')
                        ->disabled()
                        ->color('gray'),

                    // Ações de publicação
                    Action::make('publicar_agora')
                        ->label('Publicar Agora')
                        ->icon('heroicon-o-megaphone')
                        ->color('success')
                        ->visible(fn(Noticia $record): bool => in_array($record->status, ['rascunho', 'agendado']))
                        ->requiresConfirmation()
                        ->modalHeading('Publicar Notícia Agora')
                        ->modalDescription('A notícia ficará imediatamente visível no aplicativo.')
                        ->action(function (Noticia $record) {
                            $record->update([
                                'status' => 'publicado',
                                'data_publicacao' => now(),
                            ]);
                        })
                        ->successNotificationTitle('Notícia publicada com sucesso!'),

                    Action::make('agendar')
                        ->label('Agendar')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->visible(fn(Noticia $record): bool => $record->status === 'rascunho')
                        ->schema([
                            DateTimePicker::make('data_agendamento')
                                ->label('Agendar para')
                                ->required()
                                ->minDate(now())
                                ->default(now()->addHour()),
                        ])
                        ->action(function (Noticia $record, array $data) {
                            $record->update([
                                'status' => 'agendado',
                                'data_agendamento' => $data['data_agendamento'],
                            ]);
                        })
                        ->successNotificationTitle('Notícia agendada com sucesso!'),

                    Action::make('despublicar')
                        ->label('Voltar p/ Rascunho')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->visible(fn(Noticia $record): bool => in_array($record->status, ['publicado', 'agendado']))
                        ->requiresConfirmation()
                        ->modalHeading('Voltar para Rascunho')
                        ->modalDescription('A notícia será removida do aplicativo e voltará para edição.')
                        ->action(function (Noticia $record) {
                            $record->update([
                                'status' => 'rascunho',
                                'data_publicacao' => null,
                                'data_agendamento' => null,
                            ]);
                        })
                        ->successNotificationTitle('Notícia movida para rascunho!'),

                    Action::make('arquivar')
                        ->label('Arquivar')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->visible(fn(Noticia $record): bool => $record->status === 'publicado')
                        ->requiresConfirmation()
                        ->modalHeading('Arquivar Notícia')
                        ->modalDescription('A notícia será removida do aplicativo mas mantida no sistema.')
                        ->action(function (Noticia $record) {
                            $record->update(['status' => 'arquivado']);
                        })
                        ->successNotificationTitle('Notícia arquivada!'),

                    Action::make('desarquivar')
                        ->label('📤 Desarquivar')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('info')
                        ->visible(fn(Noticia $record): bool => $record->status === 'arquivado')
                        ->action(function (Noticia $record) {
                            $record->update(['status' => 'rascunho']);
                        })
                        ->successNotificationTitle('Notícia desarquivada!'),

                    // Separator
                    Action::make('separator2')
                        ->label('─────── Outros ───────')
                        ->disabled()
                        ->color('gray'),

                    DeleteAction::make(),
                ])
                    ->label('Ações')
                    ->button()
                    ->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
