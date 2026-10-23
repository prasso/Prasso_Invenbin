<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckBox;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductDescriptorsRelationManager extends RelationManager
{
    protected static string $relationship = 'descriptors';

    public function form(Schema $schema): Schema
    {
        return $schema
            
                ->components([
                    Hidden::make('id'),
                    Hidden::make('erp_product_id'),
                    TextInput::make('title'),
                    TextInput::make('descriptor'),
                    CheckBox::make('is_bulleted_list'),
                    TextInput::make('list_order'),
                ])
           ;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Title')
            ->columns([
                TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
