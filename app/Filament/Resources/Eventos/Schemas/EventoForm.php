<?php

namespace App\Filament\Resources\Eventos\Schemas;

use App\Models\Parlamentar;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;

class EventoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Dados do Evento')
                    ->tabs([
                        Tab::make('Informações Básicas')
                            ->schema([
                                TextInput::make('titulo')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Textarea::make('descricao')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                RichEditor::make('detalhes')
                                    ->label('Detalhes Completos')
                                    ->columnSpanFull(),

                                Select::make('tipo')
                                    ->options([
                                        'sessao_ordinaria' => 'Sessão Ordinária',
                                        'sessao_extraordinaria' => 'Sessão Extraordinária',
                                        'sessao_solene' => 'Sessão Solene',
                                        'audiencia_publica' => 'Audiência Pública',
                                        'reuniao_comissao' => 'Reunião de Comissão',
                                        'evento_especial' => 'Evento Especial',
                                        'feriado' => 'Feriado',
                                        'recesso' => 'Recesso',
                                    ])
                                    ->required(),

                                Select::make('status')
                                    ->options([
                                        'agendado' => 'Agendado',
                                        'em_andamento' => 'Em Andamento',
                                        'finalizado' => 'Finalizado',
                                        'cancelado' => 'Cancelado',
                                        'adiado' => 'Adiado',
                                    ])
                                    ->required()
                                    ->default('agendado'),
                            ])
                            ->columns(2),

                        Tab::make('Data e Horário')
                            ->schema([
                                DatePicker::make('data')
                                    ->required()
                                    ->default(now()),

                                Toggle::make('dia_todo')
                                    ->label('Evento de dia todo')
                                    ->reactive(),

                                TimePicker::make('hora_inicio')
                                    ->label('Hora de Início')
                                    ->required(fn(Get $get): bool => !$get('dia_todo'))
                                    ->hidden(fn(Get $get): bool => $get('dia_todo')),

                                TimePicker::make('hora_fim')
                                    ->label('Hora de Fim')
                                    ->hidden(fn(Get $get): bool => $get('dia_todo')),
                            ])
                            ->columns(2),

                        Tab::make('Local e Participantes')
                            ->schema([
                                TextInput::make('local')
                                    ->maxLength(255),

                                TextInput::make('endereco')
                                    ->maxLength(255),

                                Select::make('parlamentares_envolvidos')
                                    ->label('Parlamentares Envolvidos')
                                    ->options(Parlamentar::where('ativo_app', true)->pluck('nome_parlamentar', 'id'))
                                    ->multiple()
                                    ->searchable()
                                    ->columnSpanFull(),

                                Select::make('projeto_relacionado_id')
                                    ->label('Projeto Relacionado')
                                    ->relationship('projetoRelacionado', 'titulo')
                                    ->searchable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Tab::make('Configurações')
                            ->schema([
                                Toggle::make('publico')
                                    ->label('Aberto ao Público')
                                    ->default(true),

                                Toggle::make('transmissao_online')
                                    ->label('Transmissão Online')
                                    ->reactive(),

                                TextInput::make('link_transmissao')
                                    ->label('Link da Transmissão')
                                    ->url()
                                    ->visible(fn(Get $get): bool => $get('transmissao_online')),

                                ColorPicker::make('cor_evento')
                                    ->label('Cor no Calendário')
                                    ->default('#3B82F6'),

                                Toggle::make('destaque')
                                    ->label('Destacar no App'),

                                Toggle::make('notificar_usuarios')
                                    ->label('Enviar Notificação'),

                                TextInput::make('ordem_exibicao')
                                    ->label('Ordem de Exibição')
                                    ->numeric()
                                    ->default(0),

                                Textarea::make('observacoes')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
            ]);
    }
}
