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
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages\ListErpProductUsageLogs;
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages\CreateErpProductUsageLog;
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages\EditErpProductUsageLog;
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProductUsageLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductUsageLogResource extends Resource
{
    protected static ?string $model = ErpProductUsageLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('updated_by')
                    ->relationship('updatedBy', 'id'),
                TextInput::make('product_used_on'),
                TextInput::make('adjustment'),
                Select::make('product_id')
                    ->relationship('product', 'id')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_updated.id'),
                TextColumn::make('product_used_on'),
                TextColumn::make('adjustment'),
                TextColumn::make('products.id')
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
            'index' => ListErpProductUsageLogs::route('/'),
            'create' => CreateErpProductUsageLog::route('/create'),
            'edit' => EditErpProductUsageLog::route('/{record}/edit'),
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
