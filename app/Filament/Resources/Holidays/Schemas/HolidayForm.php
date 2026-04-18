<?php

namespace App\Filament\Resources\Holidays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HolidayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('holiday_date')
                    ->required(),
                TextInput::make('holiday_note')
                    ->required()
            ])
            ->columns(1);
    }
}
