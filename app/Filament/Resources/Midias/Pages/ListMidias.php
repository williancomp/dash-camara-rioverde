<?php

namespace App\Filament\Resources\Midias\Pages;

use App\Filament\Resources\Midias\MidiaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMidias extends ListRecords
{
    protected static string $resource = MidiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
