<?php

namespace App\Filament\Resources\PontoInteresses\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Support\Str;

class PontoInteresseForm
{


    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dados do Ponto de Interesse')
                    ->tabs([
                        Tab::make('Informações Básicas')
                            ->schema([
                                TextInput::make('nome')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn(string $operation, $state, Set $set) =>
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                    )
                                    ->columnSpanFull(),

                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),

                                Textarea::make('descricao')
                                    ->maxLength(1000)
                                    ->rows(3)
                                    ->columnSpanFull(),



                                OptimizedImageUpload::make('foto_principal')
                                    ->disk('public')
                                    ->label('Foto Principal')
                                    ->image()
                                    ->imageEditor()
                                    ->multiple(false)
                                    ->directory('pontos-interesse/principal')
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072)
                                    ->quality(50)
                                    ->showCompressionStats(),

                                OptimizedImageUpload::make('galeria_fotos')
                                    ->disk('public')
                                    ->label('Galeria de Fotos')
                                    ->image()
                                    ->imageCropAspectRatio('1:1') // Mantenha isso
                                    ->imageEditor()
                                    ->panelAspectRatio('16:9') // Mantenha isso
                                    ->panelLayout('grid')
                                    ->multiple(true)
                                    ->directory('pontos-interesse/galeria')
                                    ->reorderable()
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072) // 3MB
                                    ->maxFiles(5)
                                    ->multiple(true)
                                    ->quality(50)
                                    ->showCompressionStats()
                                    ->helperText('Fotos adicionais que aparecerão na notícia.'),


                            ]),

                        Tab::make('Localização')
                            ->schema([
                                TextInput::make('endereco_completo')
                                    ->label('Endereço Completo')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('bairro')
                                    ->required()
                                    ->maxLength(100),

                                TextInput::make('cep')
                                    ->label('CEP')
                                    ->mask('99999-999'),

                                TextInput::make('referencia')
                                    ->label('Ponto de Referência')
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->placeholder('Ex: Próximo ao mercado central'),

                                TextInput::make('latitude')
                                    ->required()
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step(0.00000001)
                                    ->minValue(-90)
                                    ->maxValue(90)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state && (floatval($state) < -90 || floatval($state) > 90)) {
                                            $set('latitude', '');
                                        }
                                    })
                                    ->rules(['regex:/^-?([0-8]?[0-9](\.[0-9]{1,8})?|90(\.0{1,8})?)$/'])
                                    ->validationMessages([
                                        'regex' => 'Latitude deve estar entre -90 e 90 graus com até 8 casas decimais',
                                    ])
                                    ->helperText('Coordenada de latitude (ex: -14.235004) - Entre -90 e 90 graus')
                                    ->placeholder('-14.235004'),

                                TextInput::make('longitude')
                                    ->required()
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->step(0.00000001)
                                    ->minValue(-180)
                                    ->maxValue(180)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state && (floatval($state) < -180 || floatval($state) > 180)) {
                                            $set('longitude', '');
                                        }
                                    })
                                    ->rules(['regex:/^-?((1[0-7][0-9](\.[0-9]{1,8})?)|([0-9]?[0-9](\.[0-9]{1,8})?)|180(\.0{1,8})?)$/'])
                                    ->validationMessages([
                                        'regex' => 'Longitude deve estar entre -180 e 180 graus com até 8 casas decimais',
                                    ])
                                    ->helperText('Coordenada de longitude (ex: -51.925282) - Entre -180 e 180 graus')
                                    ->placeholder('-51.925282'),
                            ])
                            ->columns(2),

                        Tab::make('Categorização')
                            ->schema([
                                Select::make('categoria')
                                    ->options([
                                        'educacao' => '🏫 Educação',
                                        'saude' => '🏥 Saúde',
                                        'lazer_esporte' => '⚽ Lazer e Esporte',
                                        'servicos_publicos' => '🏛️ Serviços Públicos',
                                        'transporte' => '🚌 Transporte',
                                        'seguranca' => '👮 Segurança',
                                        'cultura' => '🎭 Cultura',
                                        'assistencia_social' => '🤝 Assistência Social',
                                        'meio_ambiente' => '🌱 Meio Ambiente',
                                        'legislativo' => '⚖️ Legislativo',
                                        'obras_andamento' => '🚧 Obras em Andamento',
                                        'locais_votacao' => '🗳️ Locais de Votação',
                                        'turismo' => '📍 Turismo',
                                        'religioso' => '⛪ Religioso',
                                        'comercio_servicos' => '🏪 Comércio e Serviços',
                                    ])
                                    ->required()
                                    ->live(),

                                Select::make('subcategoria')
                                    ->options(function (Get $get) {
                                        return match ($get('categoria')) {
                                            'educacao' => [
                                                'escola_municipal' => 'Escola Municipal',
                                                'escola_estadual' => 'Escola Estadual',
                                                'escola_particular' => 'Escola Particular',
                                                'creche' => 'Creche',
                                                'universidade' => 'Universidade',
                                                'biblioteca' => 'Biblioteca',
                                            ],
                                            'saude' => [
                                                'ubs_posto_saude' => 'UBS/Posto de Saúde',
                                                'hospital' => 'Hospital',
                                                'clinica' => 'Clínica',
                                                'farmacia_popular' => 'Farmácia Popular',
                                                'consultorio' => 'Consultório',
                                            ],
                                            'lazer_esporte' => [
                                                'praca' => 'Praça',
                                                'parque' => 'Parque',
                                                'quadra_esportiva' => 'Quadra Esportiva',
                                                'ginasio' => 'Ginásio',
                                                'campo_futebol' => 'Campo de Futebol',
                                                'pista_caminhada' => 'Pista de Caminhada',
                                                'playground' => 'Playground',
                                            ],
                                            'servicos_publicos' => [
                                                'prefeitura' => 'Prefeitura',
                                                'cartorio' => 'Cartório',
                                                'correios' => 'Correios',
                                                'banco' => 'Banco',
                                                'caixa_eletronico' => 'Caixa Eletrônico',
                                                'detran' => 'DETRAN',
                                                'forum' => 'Fórum',
                                                'defensoria' => 'Defensoria Pública',
                                            ],
                                            'transporte' => [
                                                'ponto_onibus' => 'Ponto de Ônibus',
                                                'terminal_rodoviario' => 'Terminal Rodoviário',
                                                'taxi' => 'Ponto de Táxi',
                                                'estacionamento' => 'Estacionamento Público',
                                            ],
                                            'seguranca' => [
                                                'delegacia' => 'Delegacia',
                                                'quartel_bombeiros' => 'Quartel dos Bombeiros',
                                                'guarda_municipal' => 'Guarda Municipal',
                                                'conselho_tutelar' => 'Conselho Tutelar',
                                            ],
                                            'cultura' => [
                                                'centro_cultural' => 'Centro Cultural',
                                                'museu' => 'Museu',
                                                'teatro' => 'Teatro',
                                                'cinema' => 'Cinema',
                                                'casa_cultura' => 'Casa de Cultura',
                                            ],
                                            'legislativo' => [
                                                'camara_municipal' => 'Câmara Municipal',
                                                'gabinete_vereador' => 'Gabinete de Vereador',
                                                'plenario' => 'Plenário',
                                            ],
                                            'obras_andamento' => [
                                                'obra_pavimentacao' => 'Pavimentação',
                                                'obra_saneamento' => 'Saneamento',
                                                'obra_construcao' => 'Construção',
                                                'obra_reforma' => 'Reforma',
                                            ],
                                            default => [],
                                        };
                                    })
                                    ->required()
                                    ->visible(fn(Get $get): bool => !empty($get('categoria'))),

                                Select::make('status')
                                    ->options([
                                        'ativo' => '✅ Ativo',
                                        'inativo' => '❌ Inativo',
                                        'em_obras' => '🚧 Em Obras',
                                        'temporariamente_fechado' => '⏸️ Temporariamente Fechado',
                                    ])
                                    ->required()
                                    ->default('ativo'),
                            ])
                            ->columns(3),

                        Tab::make('Contato e Horários')
                            ->schema([
                                TextInput::make('telefone')
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
                                    ->maxLength(15),

                                TextInput::make('whatsapp')
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
                                    ->maxLength(15),





                                TextInput::make('email')
                                    ->email(),

                                TextInput::make('site')
                                    ->url()
                                    ->placeholder('https://...'),

                                TextInput::make('instagram')
                                    ->prefix('@')
                                    ->placeholder('usuario'),

                                TextInput::make('facebook')
                                    ->placeholder('https://facebook.com/...'),

                                Toggle::make('funciona_24h')
                                    ->label('Funciona 24 horas')
                                    ->columnSpanFull(),

                                Textarea::make('observacoes_horario')
                                    ->label('Observações sobre Horário')
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->placeholder('Ex: Fecha aos domingos, horário especial no verão'),
                            ])
                            ->columns(2),

                        Tab::make('Características')
                            ->schema([
                                Toggle::make('acessibilidade')
                                    ->label('♿ Acessibilidade')
                                    ->helperText('Local acessível para pessoas com deficiência'),

                                Toggle::make('estacionamento')
                                    ->label('🅿️ Estacionamento')
                                    ->helperText('Possui estacionamento disponível'),

                                Toggle::make('wifi_publico')
                                    ->label('📶 Wi-Fi Público')
                                    ->helperText('Oferece internet Wi-Fi gratuita'),

                                TextInput::make('capacidade')
                                    ->label('👥 Capacidade')
                                    ->numeric()
                                    ->placeholder('Número de pessoas')
                                    ->helperText('Para escolas, eventos, etc.'),

                                TagsInput::make('servicos_oferecidos')
                                    ->label('🔧 Serviços Oferecidos')
                                    ->placeholder('Digite um serviço e pressione Enter')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Configurações')
                            ->schema([
                                Toggle::make('destaque')
                                    ->label('⭐ Destacar no Mapa')
                                    ->helperText('Aparece em destaque no aplicativo'),

                                Toggle::make('verificado')
                                    ->label('✅ Verificado')
                                    ->helperText('Local verificado pela equipe'),

                                TextInput::make('ordem_exibicao')
                                    ->label('📋 Ordem de Exibição')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Menor número aparece primeiro'),

                                Textarea::make('observacoes_internas')
                                    ->label('📝 Observações Internas')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Notas para uso interno da equipe'),

                                TextInput::make('responsavel_cadastro')
                                    ->label('👤 Responsável pelo Cadastro')
                                    ->default(auth()->user()?->name)
                                    ->disabled(),
                            ])
                            ->columns(2),

                        Tab::make('SEO')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('🔍 Título SEO')
                                    ->maxLength(60)
                                    ->columnSpanFull()
                                    ->helperText('Deixe vazio para usar o nome do local'),

                                Textarea::make('meta_description')
                                    ->label('🔍 Descrição SEO')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Deixe vazio para usar a descrição'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
