<?php

namespace App\Filament\Resources\EmployeeSalaries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeSalariesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user','name')
                    ->searchable()
                    ->required()
                    ->preload(),
                DatePicker::make('start_date')
                    ->label('Berlaku Dari')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Berakhir'),
                Repeater::make('rows')
                    ->relationship()
                    ->schema([
                        Select::make('salary_id')
                            ->relationship('salary', 'salary_name')
                            ->required()
                            ->distinct(),
                        TextInput::make('value')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                    ])
                    ->grid(2)
                    ->columnSpanFull()
                    ->addActionLabel('Add New Salary')
                    ->defaultItems(1)
                    ->reorderable()
                    ->collapsible()
            ])
            ->columns(3);
    }
}
