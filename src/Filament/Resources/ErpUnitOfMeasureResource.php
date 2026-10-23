<?php

namespace Faxt\Invenbin\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\Pages\ListErpUnitOfMeasures;
use Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\Pages\CreateErpUnitOfMeasure;
use Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\Pages\EditErpUnitOfMeasure;
use Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\RelationManagers;
use Faxt\Invenbin\Models\ErpUnitOfMeasure;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class ErpUnitOfMeasureResource extends Resource
{
    protected static ?string $model = ErpUnitOfMeasure::class;

    public static $routePrefix = 'erp-unit-of-measure'; // Define your custom route prefix

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('id'),
                TextInput::make('name'),
                TextInput::make('symbol'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('symbol'),
                TextColumn::make('updated_at'),
                TextColumn::make('edit'),
            ])
            
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListErpUnitOfMeasures::route('/'),
            'create' => CreateErpUnitOfMeasure::route('/create'),
            'edit' => EditErpUnitOfMeasure::route('/{record}/edit'),
        ];
    }
}
