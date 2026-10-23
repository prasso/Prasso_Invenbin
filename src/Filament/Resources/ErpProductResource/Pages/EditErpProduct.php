<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Faxt\Invenbin\Filament\Resources\ErpProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpProduct extends EditRecord
{
    protected static string $resource = ErpProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
