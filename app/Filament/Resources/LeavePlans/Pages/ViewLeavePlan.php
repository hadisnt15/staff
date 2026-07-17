<?php

namespace App\Filament\Resources\LeavePlans\Pages;

use App\Filament\Resources\LeavePlans\LeavePlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLeavePlan extends ViewRecord
{
    protected static string $resource = LeavePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

}
