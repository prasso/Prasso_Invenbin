<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages;

use Filament\Actions\CreateAction;
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpProductStatuses extends ListRecords
{
    protected static string $resource = ErpProductStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
