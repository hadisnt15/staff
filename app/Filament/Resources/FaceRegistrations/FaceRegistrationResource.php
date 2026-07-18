<?php

namespace App\Filament\Resources\FaceRegistrations;

use App\Filament\Resources\FaceRegistrations\Pages\CreateFaceRegistration;
use App\Filament\Resources\FaceRegistrations\Pages\EditFaceRegistration;
use App\Filament\Resources\FaceRegistrations\Pages\ListFaceRegistrations;
use App\Filament\Resources\FaceRegistrations\Pages\ViewFaceRegistration;
use App\Filament\Resources\FaceRegistrations\Schemas\FaceRegistrationForm;
use App\Filament\Resources\FaceRegistrations\Schemas\FaceRegistrationInfolist;
use App\Filament\Resources\FaceRegistrations\Tables\FaceRegistrationsTable;
use App\Models\FaceRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FaceRegistrationResource extends Resource
{
    protected static ?string $model = FaceRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Face Registration';

    protected static ?string $navigationLabel = 'Registrasi Wajah';

    protected static ?string $pluralModelLabel = 'Registrasi Wajah';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return FaceRegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FaceRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FaceRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaceRegistrations::route('/'),
            'create' => CreateFaceRegistration::route('/create'),
            'view' => ViewFaceRegistration::route('/{record}'),
            'edit' => EditFaceRegistration::route('/{record}/edit'),
        ];
    }
}
