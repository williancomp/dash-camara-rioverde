<?php

namespace App\Filament\Resources\PontoInteresses\Tables;

use App\Models\PontoInteresse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

class PontoInteressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_principal')
                    ->circular()
                    ->disk('public')
                    ->imageSize(50)
                    ->visibility('private')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),

                TextColumn::make('categoria')
                    ->badge()
                    ->formatStateUsing(function (string $state, PontoInteresse $record): string {
                        $icones = [
                            'educacao' => '🎓',
                            'saude' => '🏥',
                            'lazer_esporte' => '⚽',
                            'servicos_publicos' => '🏛️',
                            'transporte' => '🚌',
                            'seguranca' => '🚔',
                            'cultura' => '🎭',
                            'assistencia_social' => '🤝',
                            'meio_ambiente' => '🌱',
                            'legislativo' => '⚖️',
                            'obras_andamento' => '🚧',
                            'locais_votacao' => '🗳️',
                            'turismo' => '📸',
                            'religioso' => '⛪',
                            'comercio_servicos' => '🏪',
                        ];

                        $icone = $icones[$state] ?? '📍';
                        $label = $record->categoria_label ?? ucfirst(str_replace('_', ' ', $state));

                        return $icone . ' ' . $label;
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'educacao' => 'info',
                        'saude' => 'success',
                        'lazer_esporte' => 'warning',
                        'servicos_publicos' => 'primary',
                        'transporte' => 'gray',
                        'seguranca' => 'danger',
                        'cultura' => 'purple',
                        'legislativo' => 'indigo',
                        'obras_andamento' => 'orange',
                        default => 'gray',
                    }),



                TextColumn::make('bairro')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn(?string $state): string => $state ?? '-'),

                TextColumn::make('endereco_completo')
                    ->label('Endereço')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn(?string $state): string => $state ?? '-'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ativo' => 'success',
                        'inativo' => 'gray',
                        'em_obras' => 'warning',
                        'temporariamente_fechado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'ativo' => 'Ativo',
                        'inativo' => 'Inativo',
                        'em_obras' => 'Em Obras',
                        'temporariamente_fechado' => 'Temporariamente Fechado',
                        default => ucfirst($state),
                    }),

                IconColumn::make('destaque')
                    ->label('⭐')
                    ->boolean(),

                IconColumn::make('verificado')
                    ->label('✅')
                    ->boolean(),

                IconColumn::make('acessibilidade')
                    ->label('♿')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('visualizacoes')
                    ->label('Views')
                    ->badge()
                    ->formatStateUsing(fn(?int $state): string => (string) ($state ?? 0))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categoria')
                    ->options([
                        'educacao' => 'Educação',
                        'saude' => 'Saúde',
                        'lazer_esporte' => 'Lazer e Esporte',
                        'servicos_publicos' => 'Serviços Públicos',
                        'transporte' => 'Transporte',
                        'seguranca' => 'Segurança',
                        'cultura' => 'Cultura',
                        'assistencia_social' => 'Assistência Social',
                        'meio_ambiente' => 'Meio Ambiente',
                        'legislativo' => 'Legislativo',
                        'obras_andamento' => 'Obras em Andamento',
                        'locais_votacao' => 'Locais de Votação',
                        'turismo' => 'Turismo',
                        'religioso' => 'Religioso',
                        'comercio_servicos' => 'Comércio e Serviços',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'ativo' => 'Ativo',
                        'inativo' => 'Inativo',
                        'em_obras' => 'Em Obras',
                        'temporariamente_fechado' => 'Temporariamente Fechado',
                    ]),

                SelectFilter::make('bairro')
                    ->options(function () {
                        return PontoInteresse::whereNotNull('bairro')
                            ->distinct('bairro')
                            ->pluck('bairro', 'bairro')
                            ->filter()
                            ->toArray();
                    }),

                TernaryFilter::make('destaque'),
                TernaryFilter::make('verificado'),
                TernaryFilter::make('acessibilidade'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // Separator
                    Action::make('separator1')
                        ->label('───── Localização ─────')
                        ->disabled()
                        ->color('gray'),

                    Action::make('ver_mapa')
                        ->label('Ver no Mapa')
                        ->icon('heroicon-o-map')
                        ->color('info')
                        ->url(
                            fn(PontoInteresse $record): string =>
                            "https://www.google.com/maps?q={$record->latitude},{$record->longitude}"
                        )
                        ->openUrlInNewTab(),

                    Action::make('verificar')
                        ->label('Marcar como Verificado')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn(PontoInteresse $record): bool => !$record->verificado)
                        ->action(function (PontoInteresse $record) {
                            $record->update([
                                'verificado' => true,
                                'data_verificacao' => now(),
                            ]);
                        }),

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
