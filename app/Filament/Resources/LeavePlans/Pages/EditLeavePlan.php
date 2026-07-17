<?php

namespace App\Filament\Resources\LeavePlans\Pages;

use App\Filament\Resources\LeavePlans\LeavePlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLeavePlan extends EditRecord
{
    protected static string $resource = LeavePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
