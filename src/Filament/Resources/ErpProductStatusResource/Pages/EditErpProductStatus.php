<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProductStatus extends EditRecord
{
    protected static string $resource = ErpProductStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
