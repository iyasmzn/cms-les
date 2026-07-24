<?php

namespace App\Filament\Resources\AcademicYears\RelationManagers;

use App\Models\RegistrationWave;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WavesRelationManager extends RelationManager
{
    protected static string $relationship = 'waves';

    protected static ?string $title = 'Gelombang Pendaftaran';

    protected static ?string $modelLabel = 'Gelombang';

    protected static ?string $pluralModelLabel = 'Gelombang';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('institution_id')
                ->label('Jenjang')
                ->relationship('institution', 'name')
                ->required()
                ->native(false)
                ->preload()
                ->helperText('Gelombang ini berlaku untuk PPDB jenjang yang dipilih.')
                ->columnSpanFull(),

            TextInput::make('name')
                ->label('Nama Gelombang')
                ->required()
                ->maxLength(100)
                ->placeholder('Gelombang 1')
                ->columnSpanFull(),

            Grid::make(2)->schema([
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai Pendaftaran')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y'),

                DatePicker::make('end_date')
                    ->label('Batas Akhir Pendaftaran')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->afterOrEqual('start_date'),
            ]),

            Grid::make(2)->schema([
                DatePicker::make('selection_date')
                    ->label('Tanggal Seleksi')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->afterOrEqual('end_date'),

                DatePicker::make('announcement_date')
                    ->label('Tanggal Pengumuman')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->afterOrEqual('selection_date'),
            ]),

            Toggle::make('is_active')
                ->label('Aktif')
                ->default(true)
                ->onColor('success')
                ->offColor('danger')
                ->helperText('Nonaktifkan untuk menutup gelombang ini secara manual.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('start_date', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Gelombang')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('institution.short_name')
                    ->label('Jenjang')
                    ->state(fn (RegistrationWave $record): ?string => $record->institution?->short_name ?? $record->institution?->name)
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('Batas Daftar')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('selection_date')
                    ->label('Seleksi')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('announcement_date')
                    ->label('Pengumuman')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_open')
                    ->label('Sedang Dibuka')
                    ->boolean()
                    ->state(fn (RegistrationWave $record): bool => $record->isOpen()),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Gelombang'),
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
}
