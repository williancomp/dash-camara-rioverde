<?php

namespace App\Filament\Resources\Solicitacaos\Pages;

use App\Filament\Resources\Solicitacaos\SolicitacaoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSolicitacao extends CreateRecord
{
    protected static string $resource = SolicitacaoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
