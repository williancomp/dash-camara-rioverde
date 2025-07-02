<?php

namespace App\Filament\Resources\Parlamentars\Pages;

use App\Filament\Resources\Parlamentars\ParlamentarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParlamentars extends ListRecords
{
    protected static string $resource = ParlamentarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
