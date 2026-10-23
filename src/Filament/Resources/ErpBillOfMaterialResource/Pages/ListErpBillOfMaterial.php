<?php

namespace Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages;

use Filament\Actions\CreateAction;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpBillOfMaterial extends ListRecords
{
    protected static string $resource = ErpBillOfMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
