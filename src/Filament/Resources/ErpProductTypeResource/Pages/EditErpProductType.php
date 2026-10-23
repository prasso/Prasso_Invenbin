<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Faxt\Invenbin\Filament\Resources\ErpProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProductType extends EditRecord
{
    protected static string $resource = ErpProductTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
