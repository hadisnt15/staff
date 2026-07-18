<?php

namespace App\Filament\Resources\UserSessions;

use App\Filament\Resources\UserSessions\Pages\CreateUserSession;
use App\Filament\Resources\UserSessions\Pages\EditUserSession;
use App\Filament\Resources\UserSessions\Pages\ListUserSessions;
use App\Filament\Resources\UserSessions\Schemas\UserSessionForm;
use App\Filament\Resources\UserSessions\Tables\UserSessionsTable;
use App\Models\UserSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserSessionResource extends Resource
{
    protected static ?string $model = UserSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Pengguna Aktif';

    protected static ?string $navigationLabel = 'Pengguna Aktif';

    protected static ?string $pluralModelLabel = 'Pengguna Aktif';
    
    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return UserSessionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserSessionsTable::configure($table);
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
            'index' => ListUserSessions::route('/'),
            'create' => CreateUserSession::route('/create'),
            'edit' => EditUserSession::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_active', true);
    }
}
