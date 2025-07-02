<?php

namespace App\Filament\Resources\PontoInteresses;


use App\Filament\Resources\PontoInteresses\Pages\CreatePontoInteresse;
use App\Filament\Resources\PontoInteresses\Pages\EditPontoInteresse;
use App\Filament\Resources\PontoInteresses\Pages\ListPontoInteresses;
use App\Filament\Resources\PontoInteresses\Schemas\PontoInteresseForm;
use App\Filament\Resources\PontoInteresses\Tables\PontoInteressesTable;
use App\Filament\Widgets\MapaDePontosWidget;
use App\Models\PontoInteresse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PontoInteresseResource extends Resource
{
    protected static ?string $model = PontoInteresse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string | UnitEnum | null $navigationGroup = 'Mapa da Cidade';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Pontos de Interesse';

    public static function form(Schema $schema): Schema
    {
        return PontoInteresseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PontoInteressesTable::configure($table);
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
            'index' => ListPontoInteresses::route('/'),
            'create' => CreatePontoInteresse::route('/create'),
            'edit' => EditPontoInteresse::route('/{record}/edit'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MapaDePontosWidget::class,
        ];
    }
}
