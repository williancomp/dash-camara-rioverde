<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\MapaStatsWidget;
use App\Filament\Widgets\MidiasStatsWidget;
use App\Filament\Widgets\NoticiasStatsWidget;
use App\Filament\Widgets\OuvidoriaStatsWidget;
use App\Filament\Widgets\SectionHeaderWidget;
use App\Filament\Widgets\TransmissaoControlWidget;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->spa()
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->unsavedChangesAlerts()
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            //->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //AccountWidget::class,
                SectionHeaderWidget::make(['title' => 'Centro de Transmissão', 'icon' => '📡']),
                TransmissaoControlWidget::class,
                SectionHeaderWidget::make(['title' => 'Gestão de Notícias', 'icon' => '📰']),
                NoticiasStatsWidget::class,
                SectionHeaderWidget::make(['title' => 'Biblioteca de Mídias', 'icon' => '🎥']),
                MidiasStatsWidget::class,
                SectionHeaderWidget::make(['title' => 'Ouvidoria Digital', 'icon' => '📢']),
                OuvidoriaStatsWidget::class,
                SectionHeaderWidget::make(['title' => 'Mapa da Cidade', 'icon' => '🗺️']),
                MapaStatsWidget::class,
                //FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
