<?php

namespace App\Filament\Resources\UserSessions\Pages;

use App\Filament\Resources\UserSessions\UserSessionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserSessions extends ListRecords
{
    protected static string $resource = UserSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
