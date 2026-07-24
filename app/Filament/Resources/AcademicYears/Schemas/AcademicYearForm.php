<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tahun Ajaran')
                ->description('Tahun ajaran ditampilkan sebagai "tahun awal/tahun akhir", contoh: 2026/2027.')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('year_start')
                            ->label('Tahun Awal')
                            ->numeric()
                            ->required()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?int $state): void {
                                if ($state !== null && blank($get('year_end'))) {
                                    $set('year_end', $state + 1);
                                }
                            })
                            ->placeholder('2026'),

                        TextInput::make('year_end')
                            ->label('Tahun Akhir')
                            ->numeric()
                            ->required()
                            ->minValue(2000)
                            ->maxValue(2100)
                            ->gt('year_start')
                            ->placeholder('2027'),
                    ]),

                    Toggle::make('is_active')
                        ->label('Tahun Ajaran Aktif')
                        ->helperText('Hanya satu tahun ajaran yang bisa aktif. Mengaktifkan ini akan menonaktifkan tahun ajaran lainnya.')
                        ->onColor('success')
                        ->offColor('gray')
                        ->default(false),
                ]),
        ]);
    }
}
