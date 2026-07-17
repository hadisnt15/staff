<?php

namespace App\Filament\Resources\FaceRegistrations\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class FaceRegistrationForm
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
                Section::make('Registrasi Wajah')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 5,
                        ])
                        ->schema([
                            Placeholder::make('front_path')
                                ->label('Depan')
                                ->content(fn ($record) =>
                                    new \Illuminate\Support\HtmlString(
                                        '<img src="'.asset('storage/'.$record->front_path).'"
                                        style="width:200px;height:200px;object-fit:cover;border-radius:10px;">'
                                    )
                                ),
                            Placeholder::make('left_path')
                                ->label('Kiri')
                                ->content(fn ($record) =>
                                    new \Illuminate\Support\HtmlString(
                                        '<img src="'.asset('storage/'.$record->left_path).'"
                                        style="width:200px;height:200px;object-fit:cover;border-radius:10px;">'
                                    )
                                ),
                            Placeholder::make('right_path')
                                ->label('Kanan')
                                ->content(fn ($record) =>
                                    new \Illuminate\Support\HtmlString(
                                        '<img src="'.asset('storage/'.$record->right_path).'"
                                        style="width:200px;height:200px;object-fit:cover;border-radius:10px;">'
                                    )
                                ),
                            Placeholder::make('up_path')
                                ->label('Atas')
                                ->content(fn ($record) =>
                                    new \Illuminate\Support\HtmlString(
                                        '<img src="'.asset('storage/'.$record->up_path).'"
                                        style="width:200px;height:200px;object-fit:cover;border-radius:10px;">'
                                    )
                                ),
                            Placeholder::make('down_path')
                                ->label('Bawah')
                                ->content(fn ($record) =>
                                    new \Illuminate\Support\HtmlString(
                                        '<img src="'.asset('storage/'.$record->down_path).'"
                                        style="width:200px;height:200px;object-fit:cover;border-radius:10px;">'
                                    )
                                ),
                        ]),
                    ]),
                Section::make('Approval')
                    ->columnSpanFull()
                    ->schema([

                        Placeholder::make('status_label')
                            ->label('Status')
                            ->content(fn ($record) => ucfirst($record->status)),


                        \Filament\Actions\Action::make('approve')
                            ->label('Setujui Registrasi Wajah')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $response = \Illuminate\Support\Facades\Http::timeout(60)
                
                                    ->attach(
                                        'front',
                                        Storage::disk('public')->get($record->front_path),
                                        'front.jpg'
                                    )

                                    ->attach(
                                        'left',
                                        Storage::disk('public')->get($record->left_path),
                                        'left.jpg'
                                    )

                                    ->attach(
                                        'right',
                                        Storage::disk('public')->get($record->right_path),
                                        'right.jpg'
                                    )

                                    ->attach(
                                        'up',
                                        Storage::disk('public')->get($record->up_path),
                                        'up.jpg'
                                    )

                                    ->attach(
                                        'down',
                                        Storage::disk('public')->get($record->down_path),
                                        'down.jpg'
                                    )

                                    ->post(
                                        'http://127.0.0.1:5000/generate-embedding',
                                        [
                                            'user_id' => $record->user_id
                                        ]
                                    );


                                if (!$response->successful()) {
                                    throw new \Exception($response->body());
                                }


                                $result = $response->json();


                                if (!$result['success']) {
                                    throw new \Exception($result['message']);
                                }


                                $record->update([
                                    'status' => 'approved'
                                ]);
                            })

                    ])
            ]);
    }
}
