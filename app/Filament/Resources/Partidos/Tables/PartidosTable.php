<?php

namespace App\Filament\Resources\Partidos\Tables;

use App\Models\Partido;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;


class PartidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->disk('public')
                    ->circular()
                    ->imageSize(40),

                TextColumn::make('sigla')
                    ->badge()
                    ->color(fn(Partido $record): string => $record->cor_oficial)
                    ->searchable(),

                TextColumn::make('nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('numero_oficial')
                    ->label('Número')
                    ->sortable(),

                TextColumn::make('parlamentares_count')
                    ->label('Parlamentares')
                    ->counts('parlamentares')
                    ->badge(),

                ToggleColumn::make('ativo'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('ativo'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
