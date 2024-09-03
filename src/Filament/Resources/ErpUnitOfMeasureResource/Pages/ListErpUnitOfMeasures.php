<?php

namespace Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpUnitOfMeasureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpUnitOfMeasures extends ListRecords
{
    protected static string $resource = ErpUnitOfMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
