<?php

namespace App\Filament\Resources\Parlamentars;

use App\Filament\Resources\Parlamentars\RelationManagers\ProjetosAutorRelationManager;
use App\Filament\Resources\Parlamentars\Pages\CreateParlamentar;
use App\Filament\Resources\Parlamentars\Pages\EditParlamentar;
use App\Filament\Resources\Parlamentars\Pages\ListParlamentars;
use App\Filament\Resources\Parlamentars\Schemas\ParlamentarForm;
use App\Filament\Resources\Parlamentars\Tables\ParlamentarsTable;
use App\Models\Parlamentar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ParlamentarResource extends Resource
{
    protected static ?string $model = Parlamentar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | UnitEnum | null $navigationGroup = 'Gestão Parlamentar';

    protected static ?string $navigationLabel = 'Parlamentares';

    protected static ?string $pluralModelLabel = 'Parlamentares';

    protected static ?string $slug = 'parlamentares';

    protected static ?int $navigationSort = 4;


    public static function form(Schema $schema): Schema
    {
        return ParlamentarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ParlamentarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProjetosAutorRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParlamentars::route('/'),
            'create' => CreateParlamentar::route('/create'),
            'edit' => EditParlamentar::route('/{record}/edit'),
        ];
    }
}
