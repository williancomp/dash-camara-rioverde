<?php

namespace App\Filament\Resources\Midias;

use App\Filament\Resources\Midias\Pages\CreateMidia;
use App\Filament\Resources\Midias\Pages\EditMidia;
use App\Filament\Resources\Midias\Pages\ListMidias;
use App\Filament\Resources\Midias\Schemas\MidiaForm;
use App\Filament\Resources\Midias\Tables\MidiasTable;
use App\Models\Midia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MidiaResource extends Resource
{
    protected static ?string $model = Midia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;


    protected static string | UnitEnum | null $navigationGroup = 'Conteúdo';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Mídias';

    public static function form(Schema $schema): Schema
    {
        return MidiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MidiasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMidias::route('/'),
            'create' => CreateMidia::route('/create'),
            'edit' => EditMidia::route('/{record}/edit'),
        ];
    }
}
