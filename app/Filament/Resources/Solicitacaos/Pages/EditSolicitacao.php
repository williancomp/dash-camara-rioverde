<?php

namespace App\Filament\Resources\Solicitacaos\Pages;

use App\Filament\Resources\Solicitacaos\SolicitacaoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSolicitacao extends EditRecord
{
    protected static string $resource = SolicitacaoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
