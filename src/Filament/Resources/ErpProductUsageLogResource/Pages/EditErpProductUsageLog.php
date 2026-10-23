<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProductUsageLog extends EditRecord
{
    protected static string $resource = ErpProductUsageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
