<?php

namespace Faxt\Invenbin\Filament\Resources;

use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\RelationManagers;
use Faxt\Invenbin\Models\ErpBillOfMaterials;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpBillOfMaterialResource extends Resource
{
    protected static ?string $model = ErpBillOfMaterials::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
                ->columns(2)
                ->schema([
                    Components\Hidden::make('id'),
                    Components\Hidden::make('guid'),
                    Components\TextInput::make('bom_name'),
                    Components\Select::make('erp_product_id')
                        ->relationship('product', 'id'),

                    Section::make()
                        ->columns(3)
                        ->schema([
                            Components\TextInput::make('created_at')->readonly(),
                            Components\TextInput::make('updated_at')->readonly(),
                            Components\TextInput::make('updated_by')->readonly(),
                        ]),
                    ]),
                    Section::make()
                    ->columns(1)
                    ->schema([Components\Repeater::make('components')
                    ->columns(3)
                    ->relationship('components')
                        ->schema([
                            Components\Hidden::make('id'),
                            Components\Hidden::make('guid'),
                            Components\Hidden::make('erp_bom_id'),
                            Components\Select::make('erp_product_id')
                                ->relationship('product', 'id')
                                ->required(true),
                            Components\TextInput::make('adjustment_units'),
                            Components\TextInput::make('item_description'),
                        ]
                    ),
                ]),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.id'),
                Tables\Columns\TextColumn::make('user_updated.id'),
                Tables\Columns\TextColumn::make('bom_name')
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
            'index' => Pages\ListErpBillOfMaterials::route('/'),
            'create' => Pages\CreateErpBillOfMaterial::route('/create'),
            'edit' => Pages\EditErpBillOfMaterial::route('/{record}/edit'),
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
