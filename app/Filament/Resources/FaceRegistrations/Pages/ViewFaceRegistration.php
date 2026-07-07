<?php

namespace App\Filament\Resources\FaceRegistrations\Pages;

use App\Filament\Resources\FaceRegistrations\FaceRegistrationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFaceRegistration extends ViewRecord
{
    protected static string $resource = FaceRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

}
