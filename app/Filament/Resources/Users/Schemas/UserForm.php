<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Database\Eloquent\Builder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nama'),
                TextInput::make('username')
                    ->required()
                    ->label('Nama Pengguna'),
                TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(15)
                    ->helperText('Contoh: 628123456789')
                    ->rule('regex:/^62[0-9]{8,13}$/'),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->required()
                    ->hiddenOn(Operation::Edit),
                Select::make('branch_id')
                    ->label('Cabang')
                    ->required()
                    ->preload()
                    ->relationship('branch','name'),
                Select::make('roles')
                    ->label('Posisi')
                    ->required()
                    ->multiple()
                    ->preload()
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('name', '!=', 'super_admin'),
                    )
            ]);
    }
}
