<?php

namespace Faxt\Invenbin\Filament\Resources;


use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProduct;    
use Filament\Forms\Components;
use Filament\Forms\Components\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductResource extends Resource
{
    protected static ?string $model = ErpProduct::class;

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
                        Components\TextInput::make('sku')->required(),
                        Components\TextInput::make('product_name')->required(),
                        Components\TextInput::make('short_description'),
                        Components\Select::make('product_status_id')
                            ->relationship('status', 'status')
                            ->required(), // Assuming product status is required
                        Components\Select::make('product_type_id')
                            ->relationship('type', 'product_type')
                            ->required(), // Assuming product type is required
                        Components\Select::make('categories')
                            ->relationship('categories', 'category_name') // Assuming 'id' is the primary key and 'name' is the display column in the categories table
                            ->multiple() // Save the selected categories to the pivot table 'product_category_maps'
                            ->required(), // Assuming at least one category is required
                        Components\TextInput::make('stock_location'),
                        Components\TextInput::make('our_price')
                            ->numeric()
                            ->required(), // Assuming our price is required
                        Components\TextInput::make('retail_price')
                            ->numeric()
                            ->required(), // Assuming retail price is required
                        Components\TextInput::make('currency_code'),
                        Components\Select::make('unit_of_measure_id')
                            ->relationship('unitOfMeasure', 'name')
                            ->required(), // Assuming unit of measure is required
                        Components\TextInput::make('admin_comments'),
                        Components\TextInput::make('list_order'),
                        Components\TextInput::make('default_image'),
                        Components\TextInput::make('owned_by'),
                        Components\TextInput::make('inventory_count'),
                        Components\TextInput::make('reorder_point'),

                        Section::make('Dimensions')
                        ->collapsible()
                        ->columns(3)
                        ->label('Dimensions') // Label for the collapsed section
                        ->collapsed(true) // Start collapsed
                        ->schema([
                            Components\TextInput::make('weight')
                                ->label('Weight')
                                ->numeric(),
                            Components\TextInput::make('length')
                                ->label('Length')
                                ->numeric(),
                            Components\TextInput::make('height')
                                ->label('Height')
                                ->numeric(),
                            Components\TextInput::make('width')
                                ->label('Width')
                                ->numeric(),
                            Components\Select::make('dimension_unit_id')
                                ->label('Dimension Unit')
                                ->relationship('dimensionUnit', 'name'),
                        ]),
                        Section::make('Descriptors')
                            ->collapsible()
                            ->label('Descriptors')
                            ->collapsed(true) 
                            ->columns(1)
                            ->schema([
                                Components\Repeater::make('descriptors')
                                    ->columns(3)
                                    ->relationship('descriptors')
                                    ->defaultItems(0)
                                    ->schema([
                                        Components\Hidden::make('id'),
                                        Components\Hidden::make('erp_product_id'),
                                        Components\TextInput::make('title'),
                                        Components\TextInput::make('descriptor'),
                                        Components\Checkbox::make('is_bulleted_list'),
                                        Components\TextInput::make('list_order'),
                                    ]),
                            ]),
                        
                        Section::make('Images')
                            ->collapsible()
                            ->label('Images')
                            ->collapsed(true) 
                            ->columns(1)
                            ->schema([
                                Components\Repeater::make('images')
                                    ->columns(3)
                                    ->relationship('images')
                                    ->defaultItems(0)
                                    ->schema([
                                        Components\Hidden::make('id'),
                                        Components\Hidden::make('erp_product_id'),
                                        Components\TextInput::make('caption'),
                                        Components\TextInput::make('image_file'),
                                        Components\TextInput::make('list_order'),
                                    ]),
                            ]),
                        Section::make()
                            ->columns(3)
                            ->schema([
                                Components\TextInput::make('created_at')->readonly(),
                                Components\TextInput::make('updated_at')->readonly(),
                                Components\TextInput::make('updated_by')->readonly(),
                            ]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('guid'),
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('default_image'),
                Tables\Columns\TextColumn::make('product_name'),
                Tables\Columns\TextColumn::make('product_type.product_type'),
                Tables\Columns\TextColumn::make('short_description'),
                Tables\Columns\TextColumn::make('product_status.status'),
                Tables\Columns\TextColumn::make('reorder_point'),
                Tables\Columns\TextColumn::make('inventory_count'),
                Tables\Columns\TextColumn::make('erp_product_category_maps.erp_category_id'),
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
            RelationManagers\CategoriesRelationManager::class,
            RelationManagers\ProductDescriptorsRelationManager::class,
            RelationManagers\ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErpProducts::route('/'),
            'create' => Pages\CreateErpProduct::route('/create'),
            'edit' => Pages\EditErpProduct::route('/{record}/edit'),
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
