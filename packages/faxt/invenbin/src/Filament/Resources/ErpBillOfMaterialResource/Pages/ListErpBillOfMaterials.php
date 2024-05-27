<?php

namespace Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpBillOfMaterials extends ListRecords
{
    protected static string $resource = ErpBillOfMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
