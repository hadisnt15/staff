<?php

namespace App\Filament\Resources\LeavePlans;

use App\Filament\Resources\LeavePlans\Pages\CreateLeavePlan;
use App\Filament\Resources\LeavePlans\Pages\EditLeavePlan;
use App\Filament\Resources\LeavePlans\Pages\ListLeavePlans;
use App\Filament\Resources\LeavePlans\Pages\ViewLeavePlan;
use App\Filament\Resources\LeavePlans\Schemas\LeavePlanForm;
use App\Filament\Resources\LeavePlans\Schemas\LeavePlanInfolist;
use App\Filament\Resources\LeavePlans\Tables\LeavePlansTable;
use App\Models\LeavePlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeavePlanResource extends Resource
{
    protected static ?string $model = LeavePlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Leave Plan';

    protected static ?string $navigationLabel = 'Rencana Cuti';

    protected static ?string $pluralModelLabel = 'Rencana Cuti';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return LeavePlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeavePlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeavePlansTable::configure($table);
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
            'index' => ListLeavePlans::route('/'),
            'create' => CreateLeavePlan::route('/create'),
            'view' => ViewLeavePlan::route('/{record}'),
            'edit' => EditLeavePlan::route('/{record}/edit'),
        ];
    }
}
