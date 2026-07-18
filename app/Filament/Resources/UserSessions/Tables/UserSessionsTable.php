<?php

namespace App\Filament\Resources\UserSessions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100])
            ->columns([
                TextColumn::make('user.name'),
                TextColumn::make('ip_address'),
                TextColumn::make('last_activity')
                    ->dateTime(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('user_agent')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
                Action::make('disconnect')
                    ->label('Disconnect')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->is_active)
                    ->action(function ($record) {
                        Cache::put(
                            'logout_reason_'.$record->session_id,
                            'admin',
                            now()->addMinutes(5)
                        );
                        $record->update([
                            'is_active' => false,
                            'disconnected_at' => now(),
                        ]);

                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
