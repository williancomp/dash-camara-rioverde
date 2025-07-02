<?php

namespace App\Filament\Resources\Solicitacaos\Tables;

use App\Models\Parlamentar;
use App\Models\Solicitacao;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SolicitacaosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('protocolo')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                TextColumn::make('tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sugestao' => 'info',
                        'reclamacao' => 'warning',
                        'denuncia' => 'danger',
                        'elogio' => 'success',
                        'pedido_informacao' => 'gray',
                        'solicitacao_servico' => 'primary',
                        'proposta_projeto' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(Solicitacao $record): string => $record->tipo_label),

                TextColumn::make('assunto')
                    ->limit(40)
                    ->searchable()
                    ->tooltip(function (Solicitacao $record): string {
                        return $record->assunto;
                    }),

                TextColumn::make('nome_cidadao_exibicao')
                    ->label('Cidadão')
                    ->searchable(['nome_cidadao']),

                TextColumn::make('categoria')
                    ->badge()
                    ->formatStateUsing(fn(Solicitacao $record): string => $record->categoria_label),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(Solicitacao $record): string => $record->status_cor)
                    ->formatStateUsing(fn(Solicitacao $record): string => $record->status_label),

                TextColumn::make('prioridade')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baixa' => 'success',
                        'media' => 'warning',
                        'alta' => 'danger',
                        'urgente' => 'danger',
                    }),

                TextColumn::make('parlamentarResponsavel.nome_parlamentar')
                    ->label('Responsável')
                    ->toggleable(),

                IconColumn::make('publica')
                    ->label('Pública')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('apoios')
                    ->badge()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Recebida em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('data_resolucao')
                    ->label('Resolvida em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'recebida' => 'Recebida',
                        'em_analise' => 'Em Análise',
                        'em_andamento' => 'Em Andamento',
                        'aguardando_informacoes' => 'Aguardando Informações',
                        'resolvida' => 'Resolvida',
                        'rejeitada' => 'Rejeitada',
                        'arquivada' => 'Arquivada',
                    ]),

                SelectFilter::make('tipo')
                    ->options([
                        'sugestao' => 'Sugestão',
                        'reclamacao' => 'Reclamação',
                        'denuncia' => 'Denúncia',
                        'elogio' => 'Elogio',
                        'pedido_informacao' => 'Pedido de Informação',
                        'solicitacao_servico' => 'Solicitação de Serviço',
                        'proposta_projeto' => 'Proposta de Projeto',
                        'outros' => 'Outros',
                    ]),

                SelectFilter::make('categoria')
                    ->options([
                        'infraestrutura' => 'Infraestrutura',
                        'saude' => 'Saúde',
                        'educacao' => 'Educação',
                        'seguranca' => 'Segurança',
                        'meio_ambiente' => 'Meio Ambiente',
                        'transporte' => 'Transporte',
                        'assistencia_social' => 'Assistência Social',
                        'cultura_esporte' => 'Cultura e Esporte',
                        'administracao' => 'Administração',
                        'fiscalizacao' => 'Fiscalização',
                        'outros' => 'Outros',
                    ]),

                SelectFilter::make('prioridade')
                    ->options([
                        'baixa' => 'Baixa',
                        'media' => 'Média',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                    ]),

                SelectFilter::make('parlamentar_responsavel_id')
                    ->label('Responsável')
                    ->relationship('parlamentarResponsavel', 'nome_parlamentar'),

                TernaryFilter::make('publica'),
                TernaryFilter::make('destaque'),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('recebida_de'),
                        DatePicker::make('recebida_ate'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query
                            ->when($data['recebida_de'], fn($q) => $q->whereDate('created_at', '>=', $data['recebida_de']))
                            ->when($data['recebida_ate'], fn($q) => $q->whereDate('created_at', '<=', $data['recebida_ate']));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),

                    // Separator
                    Action::make('separator1')
                        ->label('─────── Atribuição ───────')
                        ->disabled()
                        ->color('gray'),

                    Action::make('atribuir_responsavel')
                        ->label('👤 Atribuir Responsável')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->schema([
                            Select::make('parlamentar_id')
                                ->label('Parlamentar Responsável')
                                ->options(Parlamentar::where('ativo_app', true)->pluck('nome_parlamentar', 'id'))
                                ->required(),
                            TextInput::make('setor_responsavel')
                                ->label('Setor Responsável'),
                        ])
                        ->action(function (Solicitacao $record, array $data) {
                            $record->atribuirResponsavel($data['parlamentar_id']);
                            if (isset($data['setor_responsavel'])) {
                                $record->update(['setor_responsavel' => $data['setor_responsavel']]);
                            }
                        }),

                    // Separator
                    Action::make('separator2')
                        ->label('─────── Status ───────')
                        ->disabled()
                        ->color('gray'),

                    Action::make('resolver')
                        ->label('✅ Marcar como Resolvida')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn(Solicitacao $record): bool => !in_array($record->status, ['resolvida', 'rejeitada', 'arquivada']))
                        ->schema([
                            Textarea::make('justificativa')
                                ->label('Justificativa da Resolução')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Solicitacao $record, array $data) {
                            $record->marcarComoResolvida($data['justificativa']);
                        }),

                    Action::make('rejeitar')
                        ->label('❌ Rejeitar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn(Solicitacao $record): bool => !in_array($record->status, ['resolvida', 'rejeitada', 'arquivada']))
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('justificativa')
                                ->label('Motivo da Rejeição')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (Solicitacao $record, array $data) {
                            $record->update([
                                'status' => 'rejeitada',
                                'justificativa_status' => $data['justificativa'],
                                'data_resolucao' => now(),
                            ]);
                        }),

                    DeleteAction::make(),
                ])
                    ->label('Ações')
                    ->button()
                    ->outlined(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
