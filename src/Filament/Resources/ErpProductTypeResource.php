<?php

namespace Faxt\Invenbin\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages\ListErpProductTypes;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages\CreateErpProductType;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages\EditErpProductType;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProductType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductTypeResource extends Resource
{
    protected static ?string $model = ErpProductType::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('updated_by')
                    ->relationship('updatedBy', 'id'),
                TextInput::make('product_type')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_updated.id'),
                TextColumn::make('product_type')
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
            'index' => ListErpProductTypes::route('/'),
            'create' => CreateErpProductType::route('/create'),
            'edit' => EditErpProductType::route('/{record}/edit'),
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
