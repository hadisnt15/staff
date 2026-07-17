<?php

namespace App\Filament\Resources\LeavePlans\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeavePlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Karyawan')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Placeholder::make('user_name')
                            ->label('Nama')
                            ->content(fn ($record) => $record->user?->name ?? '-'),

                        Placeholder::make('user_role')
                            ->label('Posisi')
                            ->content(function ($record) {
                                return $record->user?->roles
                                    ->pluck('name')
                                    ->map(fn ($role) => ucwords(str_replace('_', ' ', $role)))
                                    ->join(', ') ?? '-';
                            }),
                    ]),
                Section::make('Tanggal Rencana Cuti')
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('dates')
                            ->hiddenLabel()
                            ->columns(3)
                            ->schema([
                                TextEntry::make('leave_date')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)
                                        ->locale('id')
                                        ->translatedFormat('l, d F Y')),
                            ]),
                    ]),
                Section::make('Approval')
                    ->columnSpanFull()
                    ->schema([
                        Placeholder::make('status_label')
                            ->label('Status')
                            ->content(fn ($record) => ucfirst($record->status)),
                        \Filament\Actions\Action::make('approve')
                            ->label('Setujui Rencana Cuti')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update([
                                    'status' => 'approved'
                                ]);
                            })

                    ])
            ]);
    }
}
