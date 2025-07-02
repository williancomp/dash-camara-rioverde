<?php

namespace App\Filament\Resources\PontoInteresses\Pages;

use App\Filament\Resources\PontoInteresses\PontoInteresseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPontoInteresse extends EditRecord
{
    protected static string $resource = PontoInteresseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
