<?php

namespace App\Filament\Resources\FaceRegistrations\Pages;

use App\Filament\Resources\FaceRegistrations\FaceRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFaceRegistrations extends ListRecords
{
    protected static string $resource = FaceRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
