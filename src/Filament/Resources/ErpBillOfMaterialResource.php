<?php

namespace Faxt\Invenbin\Filament\Resources;

use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages\ListErpBillOfMaterial;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages\CreateErpBillOfMaterial;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages\EditErpBillOfMaterial;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\RelationManagers;
use Faxt\Invenbin\Models\ErpBillOfMaterials;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpBillOfMaterialResource extends Resource
{
    protected static ?string $model = ErpBillOfMaterials::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getSlug(?Panel $panel = null): string
    {
        return 'erp-bill-of-material'; // override to force singular
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make()
                ->columns(2)
                ->schema([
                    Hidden::make('id'),
                    Hidden::make('guid'),
                    TextInput::make('bom_name'),
                    Select::make('erp_product_id')
                        ->relationship('product', 'id'),

                    Section::make()
                        ->columns(3)
                        ->schema([
                            TextInput::make('created_at')->readonly(),
                            TextInput::make('updated_at')->readonly(),
                            TextInput::make('updated_by')->readonly(),
                        ]),
                    ]),
                    Section::make()
                    ->columns(1)
                    ->schema([Repeater::make('components')
                    ->columns(3)
                    ->relationship('components')
                        ->schema([
                            Hidden::make('id'),
                            Hidden::make('guid'),
                            Hidden::make('erp_bom_id'),
                            Select::make('erp_product_id')
                                ->relationship('product', 'id')
                                ->required(true),
                            TextInput::make('adjustment_units'),
                            TextInput::make('item_description'),
                        ]
                    ),
                ]),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.id'),
                TextColumn::make('user_updated.id'),
                TextColumn::make('bom_name')
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => ListErpBillOfMaterial::route('/'),
            'create' => CreateErpBillOfMaterial::route('/create'),
            'edit' => EditErpBillOfMaterial::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
