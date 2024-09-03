<?php

namespace Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpBillOfMaterial extends EditRecord
{
    protected static string $resource = ErpBillOfMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
