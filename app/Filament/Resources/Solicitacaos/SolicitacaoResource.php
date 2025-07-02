<?php

namespace App\Filament\Resources\Solicitacaos;

use App\Filament\Resources\Solicitacaos\Pages\CreateSolicitacao;
use App\Filament\Resources\Solicitacaos\Pages\EditSolicitacao;
use App\Filament\Resources\Solicitacaos\Pages\ListSolicitacaos;
use App\Filament\Resources\Solicitacaos\Schemas\SolicitacaoForm;
use App\Filament\Resources\Solicitacaos\Tables\SolicitacaosTable;
use App\Models\Solicitacao;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SolicitacaoResource extends Resource
{
    protected static ?string $model = Solicitacao::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string | UnitEnum | null $navigationGroup = 'Ouvidoria Digital';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Solicitações';

    protected static ?string $modelLabel = 'Solicitação';

    protected static ?string $pluralModelLabel = 'Solicitações';

    public static function form(Schema $schema): Schema
    {
        return SolicitacaoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SolicitacaosTable::configure($table);
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
            'index' => ListSolicitacaos::route('/'),
            'create' => CreateSolicitacao::route('/create'),
            'edit' => EditSolicitacao::route('/{record}/edit'),
        ];
    }
}
