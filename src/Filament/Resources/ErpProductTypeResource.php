<?php

namespace Faxt\Invenbin\Filament\Resources;

use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProductType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductTypeResource extends Resource
{
    protected static ?string $model = ErpProductType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('updated_by')
                    ->relationship('updatedBy', 'id'),
                Forms\Components\TextInput::make('product_type')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_updated.id'),
                Tables\Columns\TextColumn::make('product_type')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListErpProductTypes::route('/'),
            'create' => Pages\CreateErpProductType::route('/create'),
            'edit' => Pages\EditErpProductType::route('/{record}/edit'),
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
