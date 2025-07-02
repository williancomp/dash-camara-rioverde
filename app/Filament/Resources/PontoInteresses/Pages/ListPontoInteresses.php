<?php

namespace App\Filament\Resources\PontoInteresses\Pages;

use App\Filament\Resources\PontoInteresses\PontoInteresseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPontoInteresses extends ListRecords
{
    protected static string $resource = PontoInteresseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
