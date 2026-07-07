<?php

namespace App\Filament\Resources\FaceRegistrations\Pages;

use App\Filament\Resources\FaceRegistrations\FaceRegistrationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFaceRegistration extends EditRecord
{
    protected static string $resource = FaceRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
