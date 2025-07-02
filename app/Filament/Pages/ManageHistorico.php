<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\KeyValue;
use App\Settings\HistoricoSettings;
use BackedEnum;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageHistorico extends SettingsPage
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string | UnitEnum | null $navigationGroup = 'Conteúdo';

    protected static ?string $navigationLabel = 'Histórico';

    protected static ?string $title = 'Histórico da Câmara e Cidade';

    protected static ?int $navigationSort = 3;

    protected static string $settings = HistoricoSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Histórico')
                    ->tabs([
                        Tab::make('História da Câmara Municipal')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([

                                TextInput::make('camara_titulo')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('camara_subtitulo')
                                    ->label('Subtítulo')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make('camara_conteudo')
                                    ->label('Conteúdo')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'heading',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'codeBlock',
                                    ]),



                                FileUpload::make('camara_imagem_destaque')
                                    ->label('Imagem de Destaque')
                                    ->image()
                                    ->directory('historico/camara')
                                    ->maxSize(3072) // 3MB
                                    ->columnSpanFull()
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->downloadable(),


                                FileUpload::make('camara_galeria_imagens')
                                    ->label('Galeria de Imagens')
                                    ->image()
                                    ->multiple()
                                    ->directory('historico/camara/galeria')
                                    ->maxSize(3072) // 3MB
                                    ->reorderable()
                                    ->columnSpanFull()
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->downloadable()
                                    ->maxFiles(5),


                                Section::make('SEO')
                                    ->schema([
                                        KeyValue::make('camara_meta_dados')
                                            ->label('Meta Dados para SEO')
                                            ->keyLabel('Propriedade')
                                            ->valueLabel('Valor')
                                            ->addActionLabel('Adicionar Meta Tag')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
                            ]),

                        Tab::make('História da Cidade')
                            ->icon('heroicon-o-building-office')
                            ->schema([

                                TextInput::make('cidade_titulo')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('cidade_subtitulo')
                                    ->label('Subtítulo')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                RichEditor::make('cidade_conteudo')
                                    ->label('Conteúdo')
                                    ->required()
                                    ->columnSpanFull()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'heading',
                                        'bulletList',
                                        'orderedList',
                                        'blockquote',
                                        'codeBlock',
                                    ]),



                                FileUpload::make('cidade_imagem_destaque')
                                    ->label('Imagem de Destaque')
                                    ->image()
                                    ->directory('historico/cidade')
                                    ->maxSize(3072) // 3MB
                                    ->columnSpanFull()
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->downloadable(),


                                FileUpload::make('cidade_galeria_imagens')
                                    ->label('Galeria de Imagens')
                                    ->image()
                                    ->multiple()
                                    ->directory('historico/cidade/galeria')
                                    ->maxSize(3072) // 3MB
                                    ->reorderable()
                                    ->columnSpanFull()
                                    ->panelLayout('grid')
                                    ->openable()
                                    ->downloadable()
                                    ->maxFiles(5),

                                Section::make('SEO')
                                    ->schema([
                                        KeyValue::make('cidade_meta_dados')
                                            ->label('Meta Dados para SEO')
                                            ->keyLabel('Propriedade')
                                            ->valueLabel('Valor')
                                            ->addActionLabel('Adicionar Meta Tag')
                                            ->columnSpanFull(),
                                    ])
                                    ->collapsed(),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull()
            ]);
    }
}
