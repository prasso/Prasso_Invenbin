<?php

namespace Faxt\Invenbin\Filament\Resources;


use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers\CategoriesRelationManager;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers\ProductDescriptorsRelationManager;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers\ImagesRelationManager;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages\ListErpProducts;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages\CreateErpProduct;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages\EditErpProduct;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers;
use Faxt\Invenbin\Models\ErpProduct;    
use Filament\Forms\Components;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpProductResource extends Resource
{
    protected static ?string $model = ErpProduct::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Hidden::make('id'),
                        Hidden::make('guid'),
                        TextInput::make('sku')->required(),
                        TextInput::make('product_name')->required(),
                        TextInput::make('short_description'),
                        Select::make('product_status_id')
                            ->relationship('status', 'status')
                            ->required(), // Assuming product status is required
                        Select::make('product_type_id')
                            ->relationship('type', 'product_type')
                            ->required(), // Assuming product type is required
                        Select::make('categories')
                            ->relationship('categories', 'category_name') // Assuming 'id' is the primary key and 'name' is the display column in the categories table
                            ->multiple() // Save the selected categories to the pivot table 'product_category_maps'
                            ->required(), // Assuming at least one category is required
                        TextInput::make('stock_location'),
                        TextInput::make('our_price')
                            ->numeric()
                            ->required(), // Assuming our price is required
                        TextInput::make('retail_price')
                            ->numeric()
                            ->required(), // Assuming retail price is required
                        TextInput::make('currency_code'),
                        Select::make('unit_of_measure_id')
                            ->relationship('unitOfMeasure', 'name')
                            ->required(), // Assuming unit of measure is required
                        TextInput::make('admin_comments'),
                        TextInput::make('list_order'),
                        TextInput::make('default_image'),
                        TextInput::make('owned_by'),
                        TextInput::make('inventory_count'),
                        TextInput::make('reorder_point'),

                        Section::make('Dimensions')
                        ->collapsible()
                        ->columns(3)
                        ->label('Dimensions') // Label for the collapsed section
                        ->collapsed(true) // Start collapsed
                        ->schema([
                            TextInput::make('weight')
                                ->label('Weight')
                                ->numeric(),
                            TextInput::make('length')
                                ->label('Length')
                                ->numeric(),
                            TextInput::make('height')
                                ->label('Height')
                                ->numeric(),
                            TextInput::make('width')
                                ->label('Width')
                                ->numeric(),
                            Select::make('dimension_unit_id')
                                ->label('Dimension Unit')
                                ->relationship('dimensionUnit', 'name'),
                        ]),
                        Section::make('Descriptors')
                            ->collapsible()
                            ->label('Descriptors')
                            ->collapsed(true) 
                            ->columns(1)
                            ->schema([
                                Repeater::make('descriptors')
                                    ->columns(3)
                                    ->relationship('descriptors')
                                    ->defaultItems(0)
                                    ->schema([
                                        Hidden::make('id'),
                                        Hidden::make('erp_product_id'),
                                        TextInput::make('title'),
                                        TextInput::make('descriptor'),
                                        Checkbox::make('is_bulleted_list'),
                                        TextInput::make('list_order'),
                                    ]),
                            ]),
                        
                        Section::make('Images')
                            ->collapsible()
                            ->label('Images')
                            ->collapsed(true) 
                            ->columns(1)
                            ->schema([
                                Repeater::make('images')
                                    ->columns(3)
                                    ->relationship('images')
                                    ->defaultItems(0)
                                    ->schema([
                                        Hidden::make('id'),
                                        Hidden::make('erp_product_id'),
                                        TextInput::make('caption'),
                                        TextInput::make('image_file'),
                                        TextInput::make('list_order'),
                                    ]),
                            ]),
                        Section::make()
                            ->columns(3)
                            ->schema([
                                TextInput::make('created_at')->readonly(),
                                TextInput::make('updated_at')->readonly(),
                                TextInput::make('updated_by')->readonly(),
                            ]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('guid'),
                TextColumn::make('sku'),
                TextColumn::make('default_image'),
                TextColumn::make('product_name'),
                TextColumn::make('product_type.product_type'),
                TextColumn::make('short_description'),
                TextColumn::make('product_status.status'),
                TextColumn::make('reorder_point'),
                TextColumn::make('inventory_count'),
                TextColumn::make('erp_product_category_maps.erp_category_id'),
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
            CategoriesRelationManager::class,
            ProductDescriptorsRelationManager::class,
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErpProducts::route('/'),
            'create' => CreateErpProduct::route('/create'),
            'edit' => EditErpProduct::route('/{record}/edit'),
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
