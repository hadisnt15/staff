<?php

namespace App\Filament\Resources\EmployeeSalaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeSalariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.id')
                    ->label('ID'),
                TextColumn::make('start_date')
                    ->label('Dari'),
                TextColumn::make('end_date')
                    ->label('Hingga'),
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable(),
                TextColumn::make('user.roles.name')
                    ->label('Posisi')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return collect($state)->implode(', ');
                    }),
                TextColumn::make('rows.salary.salary_name')
                    ->label('Komponen Gaji (IDR)')
                    ->html()
                    ->getStateUsing(function ($record) {
                        return $record->rows->map(function ($row) {
                            return "<div>{$row->salary->salary_name}: " . number_format($row->value, 0, ',', '.') . " ({$row->salary->salary_type})</div>";
                        })->implode('');
                    }),
                TextColumn::make('total_gaji')
                    ->label('Hitung Gaji (25 hari kerja)')
                    ->money('IDR', true),
            ])
            ->actions([
                EditAction::make(),
            ])

            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('created_at', 'desc')

            ->recordUrl(null)
            ->recordAction(null)

            // ->rowView('filament.employee-salary-rows')
            ;
    }
}
