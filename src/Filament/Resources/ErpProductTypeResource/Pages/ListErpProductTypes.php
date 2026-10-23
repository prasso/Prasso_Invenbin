<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages;

use Filament\Actions\CreateAction;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpProductTypes extends ListRecords
{
    protected static string $resource = ErpProductTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
