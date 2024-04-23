<?php

namespace App\Filament\Resources\ErpProductStatusResource\Pages;

use App\Filament\Resources\ErpProductStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpProductStatuses extends ListRecords
{
    protected static string $resource = ErpProductStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
