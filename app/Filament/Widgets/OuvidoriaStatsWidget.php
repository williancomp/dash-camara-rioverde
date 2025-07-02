<?php

namespace App\Filament\Widgets;

use App\Models\Solicitacao;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OuvidoriaStatsWidget extends StatsOverviewWidget
{


    protected function getStats(): array
    {
        return [
            Stat::make('📥 Novas', Solicitacao::where('status', 'recebida')->count())
                ->description('Aguardando análise')
                ->descriptionIcon('heroicon-m-inbox')
                ->color('warning')
                ->url(route('filament.admin.resources.solicitacaos.index', ['tableFilters[status][value]' => 'recebida'])),

            Stat::make('⚙️ Em Andamento', Solicitacao::emAndamento()->count())
                ->description('Sendo processadas')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('info')
                ->url(route('filament.admin.resources.solicitacaos.index', ['tableFilters[status][value]' => 'em_andamento'])),

            Stat::make('✅ Resolvidas', Solicitacao::where('status', 'resolvida')->count())
                ->description('Finalizadas com sucesso')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.solicitacaos.index', ['tableFilters[status][value]' => 'resolvida'])),

            Stat::make('📊 Total', Solicitacao::count())
                ->description('Todas as solicitações')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('gray')
                ->url(route('filament.admin.resources.solicitacaos.index')),
        ];
    }
}
