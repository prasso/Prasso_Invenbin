<?php

namespace Faxt\Invenbin\Filament\Resources\ErpProductResource\Pages;

use Filament\Actions\CreateAction;
use Faxt\Invenbin\Filament\Resources\ErpProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpProducts extends ListRecords
{
    protected static string $resource = ErpProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
