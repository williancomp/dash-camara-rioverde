<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SectionHeaderWidget extends Widget
{
    protected string $view = 'filament.widgets.section-header-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public string $title = '';
    public string $icon = '';

    // Força o widget a ocupar a linha toda
    public function getColumnSpan(): string | array | int
    {
        return 'full';
    }

    public function getViewData(): array
    {
        return [
            'title' => $this->title,
            'icon' => $this->icon,
        ];
    }
}
