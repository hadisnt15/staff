<?php

namespace App\Filament\Resources\Salaries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SalaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('salary_name')  
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('salary_code')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('salary_rule')
                    ->options([
                        'tetap' => 'Tetap',
                        'perhari' => 'Perhari',
                        'bersyarat' => 'Bersyarat',
                    ])
                    ->required(),
                Select::make('salary_type')
                    ->options([
                        'bulanan' => 'Bulanan',
                        'harian' => 'Harian',
                    ])
                    ->required(),
                Textarea::make('salary_note')
                    ->rows(3),
            ])
            ->columns(1);
    }
}
