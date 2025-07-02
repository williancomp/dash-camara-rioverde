<?php

namespace App\Filament\Resources\Projetos\Pages;

use App\Filament\Resources\Projetos\ProjetoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjeto extends CreateRecord
{
    protected static string $resource = ProjetoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
