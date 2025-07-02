<?php

namespace App\Filament\Resources\Projetos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjetoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificação do Projeto')
                    ->schema([
                        TextInput::make('numero')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: PL 001/2024'),

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
                            ->rows(3)
                            ->columnSpanFull(),

                        RichEditor::make('texto_completo')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Autoria')
                    ->schema([
                        Select::make('autor_id')
                            ->relationship('autor', 'nome_parlamentar')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Autor Principal'),

                        Select::make('coautores')
                            ->relationship('coautores', 'nome_parlamentar')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label('Coautores'),
                    ])
                    ->columns(2),

                Section::make('Status e Classificação')
                    ->schema([
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

                        Select::make('prioridade')
                            ->options([
                                'baixa' => 'Baixa',
                                'media' => 'Média',
                                'alta' => 'Alta',
                            ])
                            ->required()
                            ->default('media'),

                        Toggle::make('destaque_app')
                            ->label('Destaque no App'),
                    ])
                    ->columns(3),
            ]);
    }
}
