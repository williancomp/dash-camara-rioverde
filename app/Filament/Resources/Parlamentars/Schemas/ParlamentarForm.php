<?php

namespace App\Filament\Resources\Parlamentars\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use App\Models\Partido;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class ParlamentarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dados do Parlamentar')
                    ->tabs([
                        Tab::make('Dados Básicos')
                            ->schema([
                                TextInput::make('nome_completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('nome_parlamentar')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Nome Parlamentar (Urna)')
                                    ->columnSpanFull(),




                                OptimizedImageUpload::make('foto')
                                    ->label('Foto')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->avatar()
                                    ->imageEditor()
                                    ->multiple(false)
                                    ->directory('parlamentares/fotos')
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072)
                                    ->quality(50)
                                    ->columnSpanFull()
                                    ->showCompressionStats(),



                                Textarea::make('biografia')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Dados Políticos')
                            ->schema([
                                Select::make('partido_id')
                                    ->relationship('partido', 'sigla')
                                    ->getOptionLabelFromRecordUsing(fn(Partido $record): string => "{$record->sigla} - {$record->nome}")
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('numero_urna')
                                    ->required()
                                    ->numeric(),

                                DatePicker::make('mandato_inicio')
                                    ->required(),

                                DatePicker::make('mandato_fim')
                                    ->required(),

                                Select::make('status')
                                    ->options([
                                        'ativo' => 'Ativo',
                                        'licenciado' => 'Licenciado',
                                        'afastado' => 'Afastado',
                                    ])
                                    ->required()
                                    ->default('ativo'),

                                TextInput::make('cargo_mesa_diretora')
                                    ->maxLength(255)
                                    ->placeholder('Ex: Presidente, Vice-Presidente, Secretário'),
                            ])
                            ->columns(2),

                        Tab::make('Contato Público')
                            ->schema([
                                TextInput::make('telefone_gabinete')
                                    ->label('Telefone')
                                    ->tel()
                                    ->mask(RawJs::make(<<<'JS'
                                        $input.length <= 14 ? '(99) 9999-9999' : '(99) 99999-9999'
                                    JS))
                                    ->placeholder('(99) 99999-9999')
                                    ->stripCharacters(['(', ')', ' ', '-'])
                                    ->rules([
                                        'regex:/^\d{10,11}$/',
                                    ])
                                    ->validationMessages([
                                        'regex' => 'O telefone deve ter 10 ou 11 dígitos',
                                    ])
                                    ->maxLength(15)
                                    ->required(),


                                TextInput::make('email_oficial')
                                    ->email()
                                    ->maxLength(255),

                                TextInput::make('instagram')
                                    ->maxLength(255)
                                    ->prefix('@'),

                                TextInput::make('facebook')
                                    ->maxLength(255),

                                TextInput::make('site_pessoal')
                                    ->maxLength(255)
                                    ->url(),
                            ])
                            ->columns(2),

                        Tab::make('Configurações do App')
                            ->schema([
                                TextInput::make('ordem_exibicao')
                                    ->numeric()
                                    ->default(0)
                                    ->label('Ordem de Exibição'),

                                Toggle::make('ativo_app')
                                    ->label('Ativo no App')
                                    ->default(true),

                                ColorPicker::make('cor_perfil')
                                    ->label('Cor do Perfil')
                                    ->default('#3B82F6'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
