<?php

namespace App\Filament\Resources\Eventos\Tables;

use App\Models\Evento;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class EventosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('titulo')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sessao_ordinaria' => 'primary',
                        'sessao_extraordinaria' => 'warning',
                        'sessao_solene' => 'success',
                        'audiencia_publica' => 'info',
                        'reuniao_comissao' => 'secondary',
                        'evento_especial' => 'purple',
                        'feriado' => 'gray',
                        'recesso' => 'orange',
                    }),

                TextColumn::make('hora_inicio')
                    ->label('Horário')
                    ->formatStateUsing(function (Evento $record): string {
                        return $record->horario_formatado;
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'agendado' => 'gray',
                        'em_andamento' => 'warning',
                        'finalizado' => 'success',
                        'cancelado' => 'danger',
                        'adiado' => 'secondary',
                    }),

                IconColumn::make('publico')
                    ->label('Público')
                    ->boolean(),

                IconColumn::make('destaque')
                    ->boolean(),

                ColorColumn::make('cor_evento')
                    ->label('Cor'),
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
                        'feriado' => 'Feriado',
                        'recesso' => 'Recesso',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'agendado' => 'Agendado',
                        'em_andamento' => 'Em Andamento',
                        'finalizado' => 'Finalizado',
                        'cancelado' => 'Cancelado',
                        'adiado' => 'Adiado',
                    ]),

                TernaryFilter::make('publico')
                    ->label('Aberto ao Público'),

                TernaryFilter::make('destaque'),

                Filter::make('data')
                    ->schema([
                        DatePicker::make('data_inicio'),
                        DatePicker::make('data_fim'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when($data['data_inicio'], fn($q) => $q->whereDate('data', '>=', $data['data_inicio']))
                            ->when($data['data_fim'], fn($q) => $q->whereDate('data', '<=', $data['data_fim']));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('data', 'asc');
    }
}
