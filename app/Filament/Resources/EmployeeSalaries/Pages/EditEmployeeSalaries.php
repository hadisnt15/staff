<?php

namespace App\Filament\Resources\EmployeeSalaries\Pages;

use App\Filament\Resources\EmployeeSalaries\EmployeeSalariesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeSalaries extends EditRecord
{
    protected static string $resource = EmployeeSalariesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
