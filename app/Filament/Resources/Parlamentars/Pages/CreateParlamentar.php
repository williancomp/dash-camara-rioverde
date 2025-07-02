<?php

namespace App\Filament\Resources\Parlamentars\Pages;

use App\Filament\Resources\Parlamentars\ParlamentarResource;
use Filament\Resources\Pages\CreateRecord;

class CreateParlamentar extends CreateRecord
{
    protected static string $resource = ParlamentarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
