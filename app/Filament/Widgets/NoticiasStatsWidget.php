<?php

namespace App\Filament\Widgets;

use App\Models\Noticia;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NoticiasStatsWidget extends StatsOverviewWidget
{

    protected ?string $heading = 'Gestão de Notícias';


    protected function getStats(): array
    {
        return [
            Stat::make('📝 Rascunhos', Noticia::where('status', 'rascunho')->count())
                ->description('Notícias em edição')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray')
                ->url(route('filament.admin.resources.noticias.index', ['tableFilters[status][value]' => 'rascunho'])),

            Stat::make('⏰ Agendadas', Noticia::where('status', 'agendado')->count())
                ->description('Para publicar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.admin.resources.noticias.index', ['tableFilters[status][value]' => 'agendado'])),

            Stat::make('✅ Publicadas', Noticia::where('status', 'publicado')->count())
                ->description('Visíveis no app')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success')
                ->url(route('filament.admin.resources.noticias.index', ['tableFilters[status][value]' => 'publicado'])),

            Stat::make('📊 Total de Views', Noticia::sum('visualizacoes'))
                ->description('Visualizações totais')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
