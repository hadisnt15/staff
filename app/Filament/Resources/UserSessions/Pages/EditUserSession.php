<?php

namespace App\Filament\Resources\UserSessions\Pages;

use App\Filament\Resources\UserSessions\UserSessionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserSession extends EditRecord
{
    protected static string $resource = UserSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
