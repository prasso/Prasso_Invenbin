<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProductType extends EditRecord
{
    protected static string $resource = ErpProductTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
