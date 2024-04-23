<?php

namespace App\Filament\Resources\ErpBillOfMaterialResource\Pages;

use App\Filament\Resources\ErpBillOfMaterialResource;
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
