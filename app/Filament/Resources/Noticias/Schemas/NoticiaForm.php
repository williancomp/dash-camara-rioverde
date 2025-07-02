<?php

namespace App\Filament\Resources\Noticias\Schemas;

use App\Models\Parlamentar;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;


class NoticiaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dados da Notícia')
                    ->tabs([
                        Tab::make('Conteúdo Principal')
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

                                Textarea::make('resumo')
                                    ->required()
                                    ->maxLength(500)
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->helperText('Máximo 500 caracteres. Usado nas listagens e compartilhamentos.'),

                                RichEditor::make('conteudo')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'underline',
                                        'undo',
                                    ]),
                            ]),

                        Tab::make('Mídia')
                            ->schema([
                                FileUpload::make('foto_capa')
                                    ->label('Foto de Capa')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('noticias/capas')
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072),

                                FileUpload::make('galeria_fotos')
                                    ->label('Galeria de Fotos')
                                    ->image()
                                    ->multiple()
                                    ->directory('noticias/galeria')
                                    ->reorderable()
                                    ->columnSpanFull()
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072) // 3MB
                                    ->maxFiles(5)
                                    ->helperText('Fotos adicionais que aparecerão na notícia.'),
                            ]),

                        Tab::make('Categorização')
                            ->schema([
                                Select::make('categoria')
                                    ->options([
                                        'sessao' => 'Sessão',
                                        'projeto_lei' => 'Projeto de Lei',
                                        'audiencia_publica' => 'Audiência Pública',
                                        'evento' => 'Evento',
                                        'homenagem' => 'Homenagem',
                                        'comunicado' => 'Comunicado',
                                        'obra_publica' => 'Obra Pública',
                                        'saude' => 'Saúde',
                                        'educacao' => 'Educação',
                                        'transporte' => 'Transporte',
                                        'meio_ambiente' => 'Meio Ambiente',
                                        'social' => 'Social',
                                        'economia' => 'Economia',
                                        'cultura' => 'Cultura',
                                        'esporte' => 'Esporte',
                                        'geral' => 'Geral',
                                    ])
                                    ->required()
                                    ->default('geral'),

                                TagsInput::make('tags')
                                    ->placeholder('Digite e pressione Enter')
                                    ->helperText('Tags para melhor organização e busca.'),

                                TextInput::make('fonte')
                                    ->placeholder('Ex: Assessoria de Comunicação')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Relacionamentos')
                            ->schema([
                                Select::make('autor_parlamentar_id')
                                    ->label('Autor (Parlamentar)')
                                    ->relationship('autorParlamentar', 'nome_parlamentar')
                                    ->searchable()
                                    ->preload(),

                                Select::make('parlamentares_relacionados')
                                    ->label('Parlamentares Relacionados')
                                    ->options(Parlamentar::where('ativo_app', true)->pluck('nome_parlamentar', 'id'))
                                    ->multiple()
                                    ->searchable()
                                    ->columnSpanFull(),

                                Select::make('projeto_relacionado_id')
                                    ->label('Projeto Relacionado')
                                    ->relationship('projetoRelacionado', 'titulo')
                                    ->searchable()
                                    ->columnSpanFull(),

                                Select::make('evento_relacionado_id')
                                    ->label('Evento Relacionado')
                                    ->relationship('eventoRelacionado', 'titulo')
                                    ->searchable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Status e Publicação')
                            ->schema([
                                Section::make('Status da Notícia')
                                    ->schema([
                                        Select::make('status')
                                            ->options([
                                                'rascunho' => '📝 Rascunho',
                                                'agendado' => '⏰ Agendado para Publicar',
                                                'publicado' => '✅ Publicado',
                                                'arquivado' => '📦 Arquivado',
                                            ])
                                            ->required()
                                            ->default('rascunho')
                                            ->live()
                                            ->columnSpanFull()
                                            ->helperText(function (Get $get): string {
                                                return match ($get('status')) {
                                                    'rascunho' => 'A notícia está sendo editada e não aparece no app.',
                                                    'agendado' => 'A notícia será publicada automaticamente na data agendada.',
                                                    'publicado' => 'A notícia está visível no aplicativo.',
                                                    'arquivado' => 'A notícia foi removida do app mas mantida no sistema.',
                                                    default => ''
                                                };
                                            }),
                                    ])
                                    ->columnSpanFull(),

                                Section::make('Configurações de Data')
                                    ->schema([
                                        DateTimePicker::make('data_publicacao')
                                            ->label('📅 Data de Publicação')
                                            ->visible(fn(Get $get): bool => $get('status') === 'publicado')
                                            ->default(now())
                                            ->helperText('Quando a notícia foi/será publicada.'),

                                        DateTimePicker::make('data_agendamento')
                                            ->label('⏰ Agendar para')
                                            ->visible(fn(Get $get): bool => $get('status') === 'agendado')
                                            ->required(fn(Get $get): bool => $get('status') === 'agendado')
                                            ->minDate(now())
                                            ->helperText('A notícia será publicada automaticamente nesta data.'),
                                    ])
                                    ->columns(2)
                                    ->visible(fn(Get $get): bool => in_array($get('status'), ['publicado', 'agendado'])),

                                Section::make('Informações do Editor')
                                    ->schema([
                                        TextInput::make('editor_nome')
                                            ->label('Nome do Editor')
                                            ->default(auth()->user()?->name)
                                            ->disabled(),

                                        TextInput::make('editor_email')
                                            ->label('Email do Editor')
                                            ->email()
                                            ->default(auth()->user()?->email)
                                            ->disabled(),
                                    ])
                                    ->columns(2)
                                    ->collapsed(),
                            ]),

                        Tab::make('Configurações do App')
                            ->schema([
                                Toggle::make('destaque')
                                    ->label('Destacar na Home')
                                    ->helperText('Aparecerá em destaque no aplicativo.'),

                                Toggle::make('breaking_news')
                                    ->label('Breaking News')
                                    ->helperText('Notícia urgente - aparece no topo.'),

                                Toggle::make('notificar_usuarios')
                                    ->label('Enviar Notificação Push')
                                    ->helperText('Usuários receberão notificação no celular.'),

                                TextInput::make('ordem_destaque')
                                    ->label('Ordem de Destaque')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Menor número aparece primeiro.'),

                                Toggle::make('permitir_comentarios')
                                    ->label('Permitir Comentários')
                                    ->default(true),
                            ])
                            ->columns(2),

                        Tab::make('SEO')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Título SEO')
                                    ->maxLength(60)
                                    ->helperText('Deixe vazio para usar o título da notícia.')
                                    ->columnSpanFull(),

                                Textarea::make('meta_description')
                                    ->label('Descrição SEO')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->helperText('Deixe vazio para usar o resumo.')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
