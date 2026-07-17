<?php

namespace App\Filament\Resources\LeavePlans\Pages;

use App\Filament\Resources\LeavePlans\LeavePlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeavePlans extends ListRecords
{
    protected static string $resource = LeavePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
