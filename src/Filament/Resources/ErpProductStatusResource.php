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
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages\ListErpProductStatuses;
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages\CreateErpProductStatus;
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages\EditErpProductStatus;
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProductStatus;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductStatusResource extends Resource
{
    protected static ?string $model = ErpProductStatus::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('updated_by')
                    ->relationship('updatedBy', 'id'),
                TextInput::make('status')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_updated.id'),
                TextColumn::make('status')
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
            'index' => ListErpProductStatuses::route('/'),
            'create' => CreateErpProductStatus::route('/create'),
            'edit' => EditErpProductStatus::route('/{record}/edit'),
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
