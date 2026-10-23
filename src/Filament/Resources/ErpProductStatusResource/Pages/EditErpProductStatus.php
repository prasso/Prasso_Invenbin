<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductStatusResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Faxt\Invenbin\Filament\Resources\ErpProductStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProductStatus extends EditRecord
{
    protected static string $resource = ErpProductStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
