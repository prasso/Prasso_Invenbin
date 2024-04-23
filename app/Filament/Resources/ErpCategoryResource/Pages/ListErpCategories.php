<?php

namespace App\Filament\Resources\ErpCategoryResource\Pages;

use App\Filament\Resources\ErpCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpCategories extends ListRecords
{
    protected static string $resource = ErpCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
