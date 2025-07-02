<?php

namespace App\Filament\Resources\Partidos;

use App\Filament\Resources\Partidos\Pages\CreatePartido;
use App\Filament\Resources\Partidos\Pages\EditPartido;
use App\Filament\Resources\Partidos\Pages\ListPartidos;
use App\Filament\Resources\Partidos\Schemas\PartidoForm;
use App\Filament\Resources\Partidos\Tables\PartidosTable;
use App\Models\Partido;
use BackedEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PartidoResource extends Resource
{
    protected static ?string $model = Partido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Partidos';

    protected static string|UnitEnum|null $navigationGroup = 'Gestão Parlamentar';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return PartidoForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return PartidosTable::configure($table);
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
            'index' => ListPartidos::route('/'),
            'create' => CreatePartido::route('/create'),
            'edit' => EditPartido::route('/{record}/edit'),
        ];
    }
}
