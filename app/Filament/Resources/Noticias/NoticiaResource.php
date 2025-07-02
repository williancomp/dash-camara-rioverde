<?php

namespace App\Filament\Resources\Noticias;

use App\Filament\Resources\Noticias\Pages\CreateNoticia;
use App\Filament\Resources\Noticias\Pages\EditNoticia;
use App\Filament\Resources\Noticias\Pages\ListNoticias;
use App\Filament\Resources\Noticias\Schemas\NoticiaForm;
use App\Filament\Resources\Noticias\Tables\NoticiasTable;
use App\Models\Noticia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NoticiaResource extends Resource
{
    protected static ?string $model = Noticia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string | UnitEnum | null $navigationGroup = 'Conteúdo';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Notícias';

    public static function form(Schema $schema): Schema
    {
        return NoticiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NoticiasTable::configure($table);
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
            'index' => ListNoticias::route('/'),
            'create' => CreateNoticia::route('/create'),
            'edit' => EditNoticia::route('/{record}/edit'),
        ];
    }
}
