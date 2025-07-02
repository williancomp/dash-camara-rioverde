<?php

namespace App\Filament\Resources\Midias\Pages;

use App\Filament\Resources\Midias\MidiaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMidia extends CreateRecord
{
    protected static string $resource = MidiaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
