<?php

namespace App\Filament\Resources\Salaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('salary_name')
                    ->label('Komponen Gaji'),
                TextColumn::make('salary_oode')
                    ->label('Kode Gaji'),
                TextColumn::make('salary_type')
                    ->label('Tipe Gaji'),
                TextColumn::make('salary_rule')
                    ->label('Aturan Gaji'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
