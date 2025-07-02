<?php

namespace App\Filament\Widgets;

use App\Models\Midia;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MidiasStatsWidget extends StatsOverviewWidget
{


    protected function getStats(): array
    {
        return [
            Stat::make('🎥 Total de Mídias', Midia::ativas()->count())
                ->description('Vídeos disponíveis')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('info')
                ->url(route('filament.admin.resources.midias.index')),

            Stat::make('📺 Sessões', Midia::ativas()->whereIn('tipo', ['sessao_ordinaria', 'sessao_extraordinaria', 'sessao_solene'])->count())
                ->description('Vídeos de sessões')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->url(route('filament.admin.resources.midias.index', ['tableFilters[tipo][value]' => 'sessao_ordinaria'])),

            Stat::make('👥 Audiências', Midia::ativas()->where('tipo', 'audiencia_publica')->count())
                ->description('Audiências públicas')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->url(route('filament.admin.resources.midias.index', ['tableFilters[tipo][value]' => 'audiencia_publica'])),

            Stat::make('📊 Total de Views', Midia::sum('visualizacoes'))
                ->description('Visualizações totais')
                ->descriptionIcon('heroicon-m-eye')
                ->color('warning'),
        ];
    }
}
