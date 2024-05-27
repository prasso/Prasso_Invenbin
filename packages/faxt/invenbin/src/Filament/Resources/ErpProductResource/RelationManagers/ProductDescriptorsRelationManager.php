<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductDescriptorsRelationManager extends RelationManager
{
    protected static string $relationship = 'descriptors';

    public function form(Form $form): Form
    {
        return $form
            
                ->schema([
                    Components\Hidden::make('id'),
                    Components\Hidden::make('erp_product_id'),
                    Components\TextInput::make('title'),
                    Components\TextInput::make('descriptor'),
                    Components\CheckBox::make('is_bulleted_list'),
                    Components\TextInput::make('list_order'),
                ])
           ;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
