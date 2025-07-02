<?php

namespace App\Filament\Widgets;

use App\Models\PontoInteresse;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MapaStatsWidget extends StatsOverviewWidget
{

    protected function getStats(): array
    {
        return [
            Stat::make('🗺️ Total de Pontos', PontoInteresse::ativos()->count())
                ->description('Locais cadastrados')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info')
                ->url(route('filament.admin.resources.ponto-interesses.index')),

            Stat::make('✅ Verificados', PontoInteresse::verificados()->count())
                ->description('Pontos verificados')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->url(route('filament.admin.resources.ponto-interesses.index', ['tableFilters[verificado][value]' => '1'])),

            Stat::make('🏫 Educação', PontoInteresse::ativos()->porCategoria('educacao')->count())
                ->description('Escolas e creches')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary')
                ->url(route('filament.admin.resources.ponto-interesses.index', ['tableFilters[categoria][value]' => 'educacao'])),

            Stat::make('🏥 Saúde', PontoInteresse::ativos()->porCategoria('saude')->count())
                ->description('Postos e hospitais')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger')
                ->url(route('filament.admin.resources.ponto-interesses.index', ['tableFilters[categoria][value]' => 'saude'])),
        ];
    }
}
