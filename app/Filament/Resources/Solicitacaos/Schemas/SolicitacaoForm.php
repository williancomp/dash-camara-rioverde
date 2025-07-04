<?php

namespace App\Filament\Resources\Solicitacaos\Schemas;

use App\Filament\Forms\Components\OptimizedImageUpload;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class SolicitacaoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dados da Solicitação')
                    ->tabs([
                        Tab::make('Informações Básicas')
                            ->schema([
                                TextInput::make('protocolo')
                                    ->label('Protocolo')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Gerado automaticamente'),

                                Select::make('tipo')
                                    ->options([
                                        'sugestao' => '💡 Sugestão',
                                        'reclamacao' => '😠 Reclamação',
                                        'denuncia' => '🚨 Denúncia',
                                        'elogio' => '👏 Elogio',
                                        'pedido_informacao' => '❓ Pedido de Informação',
                                        'solicitacao_servico' => '🔧 Solicitação de Serviço',
                                        'proposta_projeto' => '📋 Proposta de Projeto',
                                        'outros' => '📁 Outros',
                                    ])
                                    ->required(),

                                Select::make('categoria')
                                    ->options([
                                        'infraestrutura' => '🏗️ Infraestrutura',
                                        'saude' => '🏥 Saúde',
                                        'educacao' => '📚 Educação',
                                        'seguranca' => '👮 Segurança',
                                        'meio_ambiente' => '🌱 Meio Ambiente',
                                        'transporte' => '🚌 Transporte',
                                        'assistencia_social' => '🤝 Assistência Social',
                                        'cultura_esporte' => '🎭 Cultura e Esporte',
                                        'administracao' => '🏛️ Administração',
                                        'fiscalizacao' => '🔍 Fiscalização',
                                        'outros' => '📁 Outros',
                                    ])
                                    ->required(),

                                TextInput::make('assunto')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make('descricao')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Dados do Cidadão')
                            ->schema([
                                TextInput::make('nome_cidadao')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email_cidadao')
                                    ->label('E-mail')
                                    ->email()
                                    ->required(),


                                TextInput::make('telefone_cidadao')
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

                                TextInput::make('cpf_cidadao')
                                    ->label('CPF')
                                    ->mask('999.999.999-99')
                                    ->rule('cpf')
                                    ->required(),




                                Textarea::make('endereco_cidadao')
                                    ->label('Endereço')
                                    ->rows(2)
                                    ->columnSpanFull(),

                                TextInput::make('bairro')
                                    ->label('Bairro'),

                                Toggle::make('identificacao_publica')
                                    ->label('Permitir identificação pública')
                                    ->helperText('Se o nome pode ser divulgado publicamente'),
                            ])
                            ->columns(2),

                        Tab::make('Localização')
                            ->schema([
                                Textarea::make('localizacao')
                                    ->label('Descrição do Local')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Descreva onde está localizado o problema/situação'),

                                TextInput::make('latitude')
                                    ->numeric()
                                    ->step(0.00000001),

                                TextInput::make('longitude')
                                    ->numeric()
                                    ->step(0.00000001),



                                FileUpload::make('anexos')
                                    ->label('Fotos/Documentos')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->multiple()
                                    ->openable()
                                    ->downloadable()
                                    ->maxSize(3072)
                                    ->maxFiles(4)
                                    ->directory('solicitacoes/anexos')

                            ])
                            ->columns(2),

                        Tab::make('Atribuição e Status')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'recebida' => '📥 Recebida',
                                        'em_analise' => '🔍 Em Análise',
                                        'em_andamento' => '⚙️ Em Andamento',
                                        'aguardando_informacoes' => '⏳ Aguardando Informações',
                                        'resolvida' => '✅ Resolvida',
                                        'rejeitada' => '❌ Rejeitada',
                                        'arquivada' => '📦 Arquivada',
                                    ])
                                    ->required()
                                    ->default('recebida'),

                                Select::make('prioridade')
                                    ->options([
                                        'baixa' => '🟢 Baixa',
                                        'media' => '🟡 Média',
                                        'alta' => '🟠 Alta',
                                        'urgente' => '🔴 Urgente',
                                    ])
                                    ->required()
                                    ->default('media'),

                                Select::make('parlamentar_responsavel_id')
                                    ->label('Parlamentar Responsável')
                                    ->relationship('parlamentarResponsavel', 'nome_parlamentar')
                                    ->searchable()
                                    ->preload(),

                                TextInput::make('setor_responsavel')
                                    ->label('Setor Responsável')
                                    ->placeholder('Ex: Secretaria de Obras, Gabinete'),

                                DateTimePicker::make('prazo_resposta')
                                    ->label('Prazo para Resposta'),

                                DateTimePicker::make('data_resolucao')
                                    ->label('Data de Resolução')
                                    ->visible(fn(Get $get): bool => in_array($get('status'), ['resolvida', 'rejeitada'])),

                                Textarea::make('justificativa_status')
                                    ->label('Justificativa/Observações')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Relacionamentos')
                            ->schema([
                                Select::make('projeto_relacionado_id')
                                    ->label('Projeto Relacionado')
                                    ->relationship('projetoRelacionado', 'titulo')
                                    ->searchable()
                                    ->columnSpanFull(),

                                TextInput::make('numero_processo')
                                    ->label('Número do Processo')
                                    ->placeholder('Se virou processo oficial')
                                    ->columnSpanFull(),

                                TagsInput::make('tags')
                                    ->label('Tags')
                                    ->placeholder('Digite e pressione Enter')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Configurações')
                            ->schema([
                                Toggle::make('publica')
                                    ->label('Tornar Pública')
                                    ->helperText('Aparecerá no portal de transparência'),

                                Toggle::make('destaque')
                                    ->label('Destacar')
                                    ->helperText('Aparece em destaque no painel'),

                                Toggle::make('notificar_cidadao')
                                    ->label('Notificar Cidadão')
                                    ->helperText('Enviar atualizações por e-mail')
                                    ->default(true),

                                TextInput::make('origem')
                                    ->label('Origem')
                                    ->default('painel')
                                    ->disabled(),

                                TextInput::make('visualizacoes')
                                    ->label('Visualizações')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),

                                TextInput::make('apoios')
                                    ->label('Apoios Recebidos')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
