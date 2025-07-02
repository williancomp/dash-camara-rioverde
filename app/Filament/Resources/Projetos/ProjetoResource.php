<?php

namespace App\Filament\Resources\Projetos;

use App\Filament\Resources\Projetos\Pages\CreateProjeto;
use App\Filament\Resources\Projetos\Pages\EditProjeto;
use App\Filament\Resources\Projetos\Pages\ListProjetos;
use App\Filament\Resources\Projetos\Schemas\ProjetoForm;
use App\Filament\Resources\Projetos\Tables\ProjetosTable;
use App\Models\Projeto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjetoResource extends Resource
{
    protected static ?string $model = Projeto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string | UnitEnum | null $navigationGroup = 'Gestão Parlamentar';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ProjetoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjetosTable::configure($table);
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
            'index' => ListProjetos::route('/'),
            'create' => CreateProjeto::route('/create'),
            'edit' => EditProjeto::route('/{record}/edit'),
        ];
    }
}
