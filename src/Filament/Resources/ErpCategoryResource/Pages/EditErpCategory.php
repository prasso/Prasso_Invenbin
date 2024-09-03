<?php

namespace Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpCategory extends EditRecord
{
    protected static string $resource = ErpCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
