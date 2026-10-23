<?php

namespace Faxt\Invenbin\Filament\Resources\ErpCategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Faxt\Invenbin\Filament\Resources\ErpCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErpCategory extends EditRecord
{
    protected static string $resource = ErpCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
