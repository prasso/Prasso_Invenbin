<?php

namespace App\Filament\Resources\ErpUnitOfMeasureResource\Pages;

use App\Filament\Resources\ErpUnitOfMeasureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpUnitOfMeasures extends ListRecords
{
    protected static string $resource = ErpUnitOfMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
