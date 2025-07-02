<?php

namespace App\Filament\Resources\Parlamentars\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjetosAutorRelationManager extends RelationManager
{
    protected static string $relationship = 'projetosAutor';

    protected static ?string $title = 'Projetos como Autor';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero')
                    ->required()
                    ->maxLength(255),

                Select::make('tipo')
                    ->options([
                        'projeto_lei' => 'Projeto de Lei',
                        'requerimento' => 'Requerimento',
                        'indicacao' => 'Indicação',
                        'mocao' => 'Moção',
                    ])
                    ->required(),

                TextInput::make('titulo')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('ementa')
                    ->required()
                    ->columnSpanFull(),

                DatePicker::make('data_apresentacao')
                    ->required()
                    ->default(now()),

                Select::make('status')
                    ->options([
                        'apresentado' => 'Apresentado',
                        'tramitando' => 'Tramitando',
                        'aprovado' => 'Aprovado',
                        'rejeitado' => 'Rejeitado',
                        'arquivado' => 'Arquivado',
                    ])
                    ->required()
                    ->default('apresentado'),

                Select::make('categoria')
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
                    ])
                    ->required()
                    ->default('outros'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('titulo')
            ->columns([
                TextColumn::make('numero')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('titulo')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'projeto_lei' => 'primary',
                        'requerimento' => 'success',
                        'indicacao' => 'warning',
                        'mocao' => 'info',
                    }),

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
            ])
            ->filters([
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
            ])
            ->headerActions([
                CreateAction::make(),
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
