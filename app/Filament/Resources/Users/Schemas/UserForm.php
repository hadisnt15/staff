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
                TextInput::make('name')->required(),
                TextInput::make('username')->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(15)
                    ->helperText('Contoh: 628123456789')
                    ->rule('regex:/^62[0-9]{8,13}$/'),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->hiddenOn(Operation::Edit),
                Select::make('roles')
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
