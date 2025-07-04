<?php

namespace App\Filament\Resources\Partidos\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
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

                        OptimizedImageUpload::make('logo')
                            ->label('Logo do Partido')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->multiple(false)
                            ->directory('partidos/logos')
                            ->openable()
                            ->downloadable()
                            ->maxSize(3072)
                            ->quality(50)
                            ->showCompressionStats(),
                        Toggle::make('ativo')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
            ]);
    }
}
