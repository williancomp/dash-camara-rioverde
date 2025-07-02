<?php

namespace App\Filament\Resources\PontoInteresses\Pages;

use App\Filament\Resources\PontoInteresses\PontoInteresseResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePontoInteresse extends CreateRecord
{
    protected static string $resource = PontoInteresseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
