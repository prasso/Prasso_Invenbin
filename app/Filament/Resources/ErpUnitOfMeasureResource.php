<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ErpUnitOfMeasureResource\Pages;
use App\Filament\Resources\ErpUnitOfMeasureResource\RelationManagers;
use App\Models\ErpUnitOfMeasure;
use Filament\Forms;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Hidden::make('id'),
                Components\TextInput::make('name'),
                Components\TextInput::make('symbol'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('symbol'),
                Tables\Columns\TextColumn::make('updated_at'),
                Tables\Columns\TextColumn::make('edit'),
            ])
            
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListErpUnitOfMeasures::route('/'),
            'create' => Pages\CreateErpUnitOfMeasure::route('/create'),
            'edit' => Pages\EditErpUnitOfMeasure::route('/{record}/edit'),
        ];
    }
}
