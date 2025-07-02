<?php

namespace App\Filament\Resources\Parlamentars\Pages;

use App\Filament\Resources\Parlamentars\ParlamentarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParlamentar extends EditRecord
{
    protected static string $resource = ParlamentarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
