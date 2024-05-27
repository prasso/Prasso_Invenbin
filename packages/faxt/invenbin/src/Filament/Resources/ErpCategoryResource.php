<?php

namespace Faxt\Invenbin\Filament\Resources;

use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource\RelationManagers;
use Faxt\Invenbin\Models\ErpCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ErpCategoryResource extends Resource
{
    protected static ?string $model = ErpCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([Section::make()
            ->columns(2)
            ->schema([
                Forms\Components\Hidden::make('id'),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'id'),
                Forms\Components\TextInput::make('image_file'),
                Forms\Components\TextInput::make('category_name'),
                Forms\Components\TextInput::make('long_description'),
                Forms\Components\TextInput::make('short_description'),
                Section::make()
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('created_at')->readonly(),
                        Forms\Components\TextInput::make('updated_at')->readonly(),
                        Forms\Components\TextInput::make('updated_by')->readonly(),
                    ]),
            ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parent.id'),
                Tables\Columns\TextColumn::make('image_file'),
                Tables\Columns\TextColumn::make('category_name'),
                Tables\Columns\TextColumn::make('user_updated.id'),
                Tables\Columns\TextColumn::make('long_description'),
                Tables\Columns\TextColumn::make('short_description')
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
            'index' => Pages\ListErpCategories::route('/'),
            'create' => Pages\CreateErpCategory::route('/create'),
            'edit' => Pages\EditErpCategory::route('/{record}/edit'),
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
