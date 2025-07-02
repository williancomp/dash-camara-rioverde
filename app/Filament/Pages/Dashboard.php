<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

class Dashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-home';
    protected static string | UnitEnum | null $navigationGroup = 'Painel de Controle';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $header = null;


    // Este método remove o título H1 da página.
    public function getHeading(): string | Htmlable
    {
        return ' '; // Retornar um espaço em branco remove o título
    }
}
