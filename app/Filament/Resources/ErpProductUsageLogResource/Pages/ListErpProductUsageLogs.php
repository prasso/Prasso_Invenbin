<?php

namespace App\Filament\Resources\ErpProductUsageLogResource\Pages;

use App\Filament\Resources\ErpProductUsageLogResource;
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
