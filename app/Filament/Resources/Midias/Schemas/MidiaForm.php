<?php

namespace App\Filament\Resources\Midias\Schemas;

use App\Models\Parlamentar;
use App\Models\Projeto;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Support\Str;
use App\Filament\Forms\Components\OptimizedImageUpload;

class MidiaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dados da Mídia')
                    ->tabs([
                        Tab::make('Informações Básicas')
                            ->schema([

                                TextInput::make('titulo')
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

                                OptimizedImageUpload::make('thumbnail')
                                    ->label('Imagem Principal')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('midias/thumbnails')
                                    ->quality(50)
                                    ->showCompressionStats()
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072) // 3MB
                                    ->helperText('Imagem que representa o vídeo. Ideal: 1280x720px'),


                                Select::make('tipo')
                                    ->options([
                                        'sessao_ordinaria' => '📋 Sessão Ordinária',
                                        'sessao_extraordinaria' => '⚡ Sessão Extraordinária',
                                        'sessao_solene' => '🎖️ Sessão Solene',
                                        'audiencia_publica' => '👥 Audiência Pública',
                                        'reuniao_comissao' => '🏛️ Reunião de Comissão',
                                        'evento_especial' => '⭐ Evento Especial',
                                        'solenidade' => '🎉 Solenidade',
                                        'entrevista' => '🎤 Entrevista',
                                        'pronunciamento' => '💬 Pronunciamento',
                                        'outros' => '📁 Outros',
                                    ])
                                    ->required(),

                                Select::make('status')
                                    ->options([
                                        'ativo' => '✅ Ativo',
                                        'inativo' => '❌ Inativo',
                                        'processando' => '⏳ Processando',
                                        'erro' => '❌ Erro',
                                    ])
                                    ->required()
                                    ->default('ativo'),
                            ])
                            ->columns(2),

                        Tab::make('Links dos Vídeos')
                            ->schema([
                                TextInput::make('youtube_url')
                                    ->label('🎥 YouTube URL')
                                    ->url()
                                    ->placeholder('https://www.youtube.com/watch?v=VIDEO_ID')
                                    ->columnSpanFull()
                                    ->helperText('O ID do vídeo será extraído automaticamente.'),

                                TextInput::make('facebook_url')
                                    ->label('📘 Facebook URL')
                                    ->url()
                                    ->placeholder('https://www.facebook.com/watch/?v=VIDEO_ID'),

                                TextInput::make('instagram_url')
                                    ->label('📷 Instagram URL')
                                    ->url()
                                    ->placeholder('https://www.instagram.com/p/POST_ID/'),

                                TextInput::make('link_alternativo')
                                    ->label('🔗 Link Alternativo')
                                    ->url()
                                    ->placeholder('Outro player ou plataforma')
                                    ->columnSpanFull(),

                                TextInput::make('duracao_segundos')
                                    ->label('⏱️ Duração (segundos)')
                                    ->numeric()
                                    ->helperText('Duração total do vídeo em segundos. Ex: 3600 = 1 hora'),

                                TextInput::make('qualidade')
                                    ->label('🎬 Qualidade')
                                    ->placeholder('Ex: HD, Full HD, 4K')
                                    ->maxLength(50),
                            ])
                            ->columns(2),

                        Tab::make('Data e Local')
                            ->schema([
                                DatePicker::make('data_evento')
                                    ->label('📅 Data do Evento')
                                    ->required()
                                    ->default(now()),

                                TimePicker::make('hora_inicio')
                                    ->label('⏰ Hora de Início'),

                                TimePicker::make('hora_fim')
                                    ->label('⏰ Hora de Fim'),

                                TextInput::make('local_evento')
                                    ->label('📍 Local do Evento')
                                    ->placeholder('Ex: Plenário da Câmara Municipal')
                                    ->columnSpanFull(),

                                TextInput::make('periodo_legislativo')
                                    ->label('🏛️ Período Legislativo')
                                    ->placeholder('Ex: 2021-2024')
                                    ->maxLength(50),
                            ])
                            ->columns(3),

                        Tab::make('Relacionamentos')
                            ->schema([
                                Select::make('parlamentares_presentes')
                                    ->label('👥 Parlamentares Presentes')
                                    ->options(Parlamentar::where('ativo_app', true)->pluck('nome_parlamentar', 'id'))
                                    ->multiple()
                                    ->searchable()
                                    ->columnSpanFull(),

                                Select::make('evento_relacionado_id')
                                    ->label('📋 Evento Relacionado')
                                    ->relationship('eventoRelacionado', 'titulo')
                                    ->searchable()
                                    ->columnSpanFull(),

                                Select::make('projetos_discutidos')
                                    ->label('📜 Projetos Discutidos')
                                    ->options(Projeto::pluck('titulo', 'id'))
                                    ->multiple()
                                    ->searchable()
                                    ->columnSpanFull(),

                                TagsInput::make('tags')
                                    ->label('🏷️ Tags')
                                    ->placeholder('Digite e pressione Enter')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Configurações do App')
                            ->schema([
                                Toggle::make('disponivel_app')
                                    ->label('📱 Disponível no App')
                                    ->default(true)
                                    ->helperText('Se a mídia aparece no aplicativo móvel.'),

                                Toggle::make('destaque')
                                    ->label('⭐ Destacar')
                                    ->helperText('Aparece em destaque no app.'),

                                TextInput::make('ordem_exibicao')
                                    ->label('📋 Ordem de Exibição')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Menor número aparece primeiro.'),

                                Textarea::make('observacoes')
                                    ->label('📝 Observações')
                                    ->maxLength(1000)
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('SEO e Metadados')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('🔍 Título SEO')
                                    ->maxLength(60)
                                    ->columnSpanFull()
                                    ->helperText('Deixe vazio para usar o título da mídia.'),

                                Textarea::make('meta_description')
                                    ->label('🔍 Descrição SEO')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Deixe vazio para usar a descrição.'),

                                TextInput::make('responsavel_upload')
                                    ->label('👤 Responsável pelo Upload')
                                    ->default(auth()->user()?->name)
                                    ->disabled(),

                                DateTimePicker::make('data_upload')
                                    ->label('📅 Data do Upload')
                                    ->default(now())
                                    ->disabled(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
