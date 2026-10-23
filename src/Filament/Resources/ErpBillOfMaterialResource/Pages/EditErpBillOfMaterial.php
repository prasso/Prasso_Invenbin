<?php

namespace Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Faxt\Invenbin\Filament\Resources\ErpBillOfMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpBillOfMaterial extends EditRecord
{
    protected static string $resource = ErpBillOfMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
