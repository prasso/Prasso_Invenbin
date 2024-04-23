<?php

namespace App\Filament\Resources\ErpProductUsageLogResource\Pages;

use App\Filament\Resources\ErpProductUsageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProductUsageLog extends EditRecord
{
    protected static string $resource = ErpProductUsageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
