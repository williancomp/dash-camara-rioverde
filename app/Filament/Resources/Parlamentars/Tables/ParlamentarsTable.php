<?php

namespace App\Filament\Resources\Parlamentars\Tables;

use App\Models\Parlamentar;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ParlamentarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->circular()
                    ->disk('public')
                    ->imageSize(50),

                TextColumn::make('nome_parlamentar')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('partido.sigla')
                    ->badge()
                    ->color(fn(Parlamentar $record): string => $record->partido->cor_oficial ?? 'gray')
                    ->sortable(),

                TextColumn::make('numero_urna')
                    ->label('Número')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ativo' => 'success',
                        'licenciado' => 'warning',
                        'afastado' => 'danger',
                    }),

                TextColumn::make('cargo_mesa_diretora')
                    ->label('Cargo')
                    ->toggleable(),

                ToggleColumn::make('ativo_app')
                    ->label('App'),

                TextColumn::make('projetos_autor_count')
                    ->label('Projetos')
                    ->counts('projetosAutor')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('partido')
                    ->relationship('partido', 'sigla'),

                SelectFilter::make('status')
                    ->options([
                        'ativo' => 'Ativo',
                        'licenciado' => 'Licenciado',
                        'afastado' => 'Afastado',
                    ]),

                TernaryFilter::make('ativo_app')
                    ->label('Ativo no App'),
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
            ->defaultSort('ordem_exibicao');
    }
}
