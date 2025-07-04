<?php

namespace App\Filament\Resources\Midias\Tables;

use App\Models\Midia;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Forms\Components\DatePicker;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;


class MidiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Capa')
                    ->disk('public')
                    ->imageSize(60)
                    ->visibility('private')
                    ->hidden(fn($record): bool => empty($record->thumbnail))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold'),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sessao_ordinaria' => 'primary',
                        'sessao_extraordinaria' => 'warning',
                        'sessao_solene' => 'success',
                        'audiencia_publica' => 'info',
                        'reuniao_comissao' => 'secondary',
                        'evento_especial' => 'purple',
                        'solenidade' => 'pink',
                        'entrevista' => 'orange',
                        'pronunciamento' => 'cyan',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(Midia $record): string => $record->tipo_label),

                TextColumn::make('data_evento')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('duracao_formatada')
                    ->label('Duração')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ativo' => 'success',
                        'inativo' => 'gray',
                        'processando' => 'warning',
                        'erro' => 'danger',
                    })
                    ->formatStateUsing(fn(Midia $record): string => $record->status_label),

                IconColumn::make('destaque')
                    ->label('⭐')
                    ->boolean(),

                IconColumn::make('disponivel_app')
                    ->label('📱')
                    ->boolean(),

                TextColumn::make('visualizacoes')
                    ->label('Views')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('youtube_video_id')
                    ->label('YouTube ID')
                    ->fontFamily('mono')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ano')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'sessao_ordinaria' => 'Sessão Ordinária',
                        'sessao_extraordinaria' => 'Sessão Extraordinária',
                        'sessao_solene' => 'Sessão Solene',
                        'audiencia_publica' => 'Audiência Pública',
                        'reuniao_comissao' => 'Reunião de Comissão',
                        'evento_especial' => 'Evento Especial',
                        'solenidade' => 'Solenidade',
                        'entrevista' => 'Entrevista',
                        'pronunciamento' => 'Pronunciamento',
                        'outros' => 'Outros',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'ativo' => 'Ativo',
                        'inativo' => 'Inativo',
                        'processando' => 'Processando',
                        'erro' => 'Erro',
                    ]),

                SelectFilter::make('ano')
                    ->options(function () {
                        $currentYear = now()->year;
                        $years = [];
                        for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                            $years[$i] = $i;
                        }
                        return $years;
                    }),

                TernaryFilter::make('destaque'),
                TernaryFilter::make('disponivel_app'),

                Filter::make('data_evento')
                    ->schema([
                        DatePicker::make('evento_de'),
                        DatePicker::make('evento_ate'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when($data['evento_de'], fn($q) => $q->whereDate('data_evento', '>=', $data['evento_de']))
                            ->when($data['evento_ate'], fn($q) => $q->whereDate('data_evento', '<=', $data['evento_ate']));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // Separator
                    Action::make('separator1')
                        ->label('─────── Links ───────')
                        ->disabled()
                        ->color('gray'),

                    Action::make('abrir_youtube')
                        ->label('YouTube')
                        ->icon('heroicon-o-play')
                        ->color('danger')
                        ->visible(fn(Midia $record): bool => !empty($record->youtube_url))
                        ->url(fn(Midia $record): string => $record->youtube_url)
                        ->openUrlInNewTab(),

                    Action::make('abrir_facebook')
                        ->label('Facebook')
                        ->icon('heroicon-o-link')
                        ->color('info')
                        ->visible(fn(Midia $record): bool => !empty($record->facebook_url))
                        ->url(fn(Midia $record): string => $record->facebook_url)
                        ->openUrlInNewTab(),

                    // Separator
                    Action::make('separator2')
                        ->label('─────── Ações ───────')
                        ->disabled()
                        ->color('gray'),

                    Action::make('ativar')
                        ->label('Ativar')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn(Midia $record): bool => $record->status !== 'ativo')
                        ->action(fn(Midia $record) => $record->update(['status' => 'ativo'])),

                    Action::make('desativar')
                        ->label('Desativar')
                        ->icon('heroicon-o-x-mark')
                        ->color('gray')
                        ->visible(fn(Midia $record): bool => $record->status === 'ativo')
                        ->requiresConfirmation()
                        ->action(fn(Midia $record) => $record->update(['status' => 'inativo'])),

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
            ->defaultSort('data_evento', 'desc');
    }
}
