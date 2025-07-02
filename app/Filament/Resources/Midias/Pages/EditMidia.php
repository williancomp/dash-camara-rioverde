<?php

namespace App\Filament\Resources\Midias\Pages;

use App\Filament\Resources\Midias\MidiaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMidia extends EditRecord
{
    protected static string $resource = MidiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
