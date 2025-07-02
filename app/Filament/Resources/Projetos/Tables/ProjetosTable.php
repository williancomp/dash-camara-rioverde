<?php

namespace App\Filament\Resources\Projetos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjetosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('titulo')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'projeto_lei' => 'primary',
                        'requerimento' => 'success',
                        'indicacao' => 'warning',
                        'mocao' => 'info',
                    }),

                TextColumn::make('autor.nome_parlamentar')
                    ->label('Autor')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'apresentado' => 'gray',
                        'tramitando' => 'warning',
                        'aprovado' => 'success',
                        'rejeitado' => 'danger',
                        'arquivado' => 'secondary',
                    }),

                TextColumn::make('categoria')
                    ->badge(),

                TextColumn::make('data_apresentacao')
                    ->date()
                    ->sortable(),

                IconColumn::make('destaque_app')
                    ->label('Destaque')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('autor')
                    ->relationship('autor', 'nome_parlamentar'),

                SelectFilter::make('tipo')
                    ->options([
                        'projeto_lei' => 'Projeto de Lei',
                        'requerimento' => 'Requerimento',
                        'indicacao' => 'Indicação',
                        'mocao' => 'Moção',
                    ]),

                SelectFilter::make('status')
                    ->options([
                        'apresentado' => 'Apresentado',
                        'tramitando' => 'Tramitando',
                        'aprovado' => 'Aprovado',
                        'rejeitado' => 'Rejeitado',
                        'arquivado' => 'Arquivado',
                    ]),

                SelectFilter::make('categoria')
                    ->options([
                        'saude' => 'Saúde',
                        'educacao' => 'Educação',
                        'transporte' => 'Transporte',
                        'meio_ambiente' => 'Meio Ambiente',
                        'seguranca' => 'Segurança',
                        'economia' => 'Economia',
                        'social' => 'Social',
                        'infraestrutura' => 'Infraestrutura',
                        'cultura' => 'Cultura',
                        'esporte' => 'Esporte',
                        'outros' => 'Outros',
                    ]),

                TernaryFilter::make('destaque_app')
                    ->label('Destaque no App'),
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
            ->defaultSort('data_apresentacao', 'desc');
    }
}
