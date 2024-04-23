<?php

namespace App\Filament\Resources\ErpProductResource\Pages;

use App\Filament\Resources\ErpProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProduct extends EditRecord
{
    protected static string $resource = ErpProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
