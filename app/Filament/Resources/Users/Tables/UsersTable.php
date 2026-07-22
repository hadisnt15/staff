<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('username')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('No. HP')
                    ->searchable(),
                TextColumn::make('branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(',')
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('resetPassword')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password User')
                    ->modalDescription('Password akan dikembalikan menjadi nomor telepon user.')
                    ->action(function ($record) {

                        if (!$record->phone) {
                            Notification::make()
                                ->title('Gagal reset password')
                                ->body('User belum memiliki nomor telepon.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'password' => Hash::make($record->phone),
                            'must_change_password' => true,
                        ]);

                        Notification::make()
                            ->title('Password berhasil direset')
                            ->body('Password baru: '.$record->phone)
                            ->success()
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
