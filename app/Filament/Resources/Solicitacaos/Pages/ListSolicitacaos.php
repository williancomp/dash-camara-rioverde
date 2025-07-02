<?php

namespace App\Filament\Resources\Solicitacaos\Pages;

use App\Filament\Resources\Solicitacaos\SolicitacaoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSolicitacaos extends ListRecords
{
    protected static string $resource = SolicitacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
