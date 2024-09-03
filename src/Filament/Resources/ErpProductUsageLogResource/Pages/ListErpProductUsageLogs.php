<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource\Pages;

use Faxt\Invenbin\Filament\Resources\ErpProductUsageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpProductUsageLogs extends ListRecords
{
    protected static string $resource = ErpProductUsageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
