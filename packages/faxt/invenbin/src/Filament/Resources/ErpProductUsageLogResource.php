<?php

namespace Faxt\Invenbin\Filament\Resources;

use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProductUsageLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductUsageLogResource extends Resource
{
    protected static ?string $model = ErpProductUsageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('updated_by')
                    ->relationship('updatedBy', 'id'),
                Forms\Components\TextInput::make('product_used_on'),
                Forms\Components\TextInput::make('adjustment'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'id')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_updated.id'),
                Tables\Columns\TextColumn::make('product_used_on'),
                Tables\Columns\TextColumn::make('adjustment'),
                Tables\Columns\TextColumn::make('products.id')
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
            'index' => Pages\ListErpProductUsageLogs::route('/'),
            'create' => Pages\CreateErpProductUsageLog::route('/create'),
            'edit' => Pages\EditErpProductUsageLog::route('/{record}/edit'),
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
