<?php

namespace App\Filament\Resources\ErpUnitOfMeasureResource\Pages;

use App\Filament\Resources\ErpUnitOfMeasureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpUnitOfMeasure extends EditRecord
{
    protected static string $resource = ErpUnitOfMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
