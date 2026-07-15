<?php

namespace App\Filament\Resources\Branches;

use App\Filament\Resources\Branches\Pages\ManageBranches;
use App\Models\Branch;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Branches';

    protected static ?string $navigationLabel = 'Cabang';

    protected static ?string $modelLabel = 'Branch';

    protected static ?string $pluralModelLabel = 'Cabang';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('lat')
                    ->numeric()
                    ->step('any'),
                TextInput::make('lng')
                    ->numeric()
                    ->step('any'),
                TextInput::make('timezone')
                    ->label('Zona Waktu')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->onColor('primary')
                    ->offColor('gray'),
                Section::make('Jam Kerja')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->schema([
                        TimePicker::make('work_start_time')
                            ->label('Jam Masuk')
                            ->seconds(false)
                            ->native(false)
                            ->required(),
                        TimePicker::make('work_end_time')
                            ->label('Jam Pulang')
                            ->seconds(false)
                            ->native(false)
                            ->required(),
                        TimePicker::make('break_start_time')
                            ->label('Jam Mulai Istirahat')
                            ->seconds(false)
                            ->native(false),
                        TimePicker::make('break_end_time')
                            ->label('Jam Selesai Istirahat')
                            ->seconds(false)
                            ->native(false),
                        TimePicker::make('work_end_time_weekend')
                            ->label('Jam Pulang (Sabtu)')
                            ->seconds(false)
                            ->native(false),
                    ])
                    ->columnSpanFull()  
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Daftar Cabang')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('lat')
                    ->label('Latitude')
                    ->searchable(),
                TextColumn::make('lng')
                    ->label('Longitude')
                    ->searchable(),
                TextColumn::make('timezone')
                    ->label('Zona Waktu')
                    ->searchable(),
                TextColumn::make('is_active')
                    ->label('Aktif'),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBranches::route('/'),
        ];
    }
}
