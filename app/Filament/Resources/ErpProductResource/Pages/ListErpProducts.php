<?php

namespace App\Filament\Resources\ErpProductResource\Pages;

use App\Filament\Resources\ErpProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErpProducts extends ListRecords
{
    protected static string $resource = ErpProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
