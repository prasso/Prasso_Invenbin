<?php

namespace Faxt\Invenbin\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages\ListErpCategories;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages\CreateErpCategory;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages\EditErpCategory;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\RelationManagers;
use Faxt\Invenbin\Models\ErpCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpCategoryResource extends Resource
{
    protected static ?string $model = ErpCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([Section::make()
            ->columns(2)
            ->schema([
                Hidden::make('id'),
                Select::make('parent_id')
                    ->relationship('parent', 'id'),
                TextInput::make('image_file'),
                TextInput::make('category_name'),
                TextInput::make('long_description'),
                TextInput::make('short_description'),
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
                TextColumn::make('parent.id'),
                TextColumn::make('image_file'),
                TextColumn::make('category_name'),
                TextColumn::make('user_updated.id'),
                TextColumn::make('long_description'),
                TextColumn::make('short_description')
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
            'index' => ListErpCategories::route('/'),
            'create' => CreateErpCategory::route('/create'),
            'edit' => EditErpCategory::route('/{record}/edit'),
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
