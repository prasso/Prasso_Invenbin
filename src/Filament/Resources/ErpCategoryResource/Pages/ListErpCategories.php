<?php

namespace Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpCategories extends ListRecords
{
    protected static string $resource = ErpCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
