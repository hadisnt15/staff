<?php

namespace App\Filament\Resources\UserSessions\Pages;

use App\Filament\Resources\UserSessions\UserSessionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserSession extends CreateRecord
{
    protected static string $resource = UserSessionResource::class;
}
