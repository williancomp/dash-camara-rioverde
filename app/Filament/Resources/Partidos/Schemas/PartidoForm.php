<?php

namespace App\Filament\Resources\Partidos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;

class PartidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Partido')
                    ->schema([
                        TextInput::make('nome')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('sigla')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('numero_oficial')
                            ->required()
                            ->numeric()
                            ->label('Número Oficial (TSE)'),

                        ColorPicker::make('cor_oficial')
                            ->required()
                            ->label('Cor Oficial'),

                        FileUpload::make('logo')
                            ->image()
                            ->imageEditor()
                            ->directory('partidos/logos')
                            ->columnSpanFull()
                            ->panelLayout('grid')
                            ->openable()
                            ->downloadable()
                            ->maxSize(3072), // 3MB



                        Toggle::make('ativo')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
            ]);
    }
}
