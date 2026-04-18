<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('announcement_title')
                    ->required()
                    ->maxLength(100),
                Textarea::make('announcement_content')
                    ->required()
                    ->rows(3)
                    ->maxLength(250),
                DatePicker::make('announcement_start_date')
                    ->required(),
                DatePicker::make('announcement_end_date')
                    ->required(),
            ])
            ->columns(1);
    }
}
