<?php

namespace App\Filament\Resources\Donations\Schemas;

use App\Models\Donation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Data Donatur')
                ->icon(Heroicon::OutlinedUser)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Nama Donatur')
                            ->required()
                            ->maxLength(150),

                        TextInput::make('phone')
                            ->label('No. HP / WhatsApp')
                            ->tel()
                            ->maxLength(30),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(150),

                        Toggle::make('is_anonymous')
                            ->label('Sembunyikan nama (anonim)')
                            ->helperText('Nama akan ditampilkan sebagai "Hamba Allah" di publik.'),
                    ]),
                ]),

            Section::make('Detail Donasi')
                ->icon(Heroicon::OutlinedBanknotes)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('amount')
                            ->label('Nominal Donasi (Rp)')
                            ->required()
                            ->numeric()
                            ->minValue(1000)
                            ->prefix('Rp'),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options(Donation::paymentMethodOptions())
                            ->required()
                            ->native(false),
                    ]),

                    Textarea::make('message')
                        ->label('Pesan / Doa')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),

            Section::make('Status & Catatan')
                ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('status')
                            ->label('Status Konfirmasi')
                            ->options(Donation::statusOptions())
                            ->required()
                            ->native(false),

                        TextInput::make('confirmed_at')
                            ->label('Tanggal Konfirmasi')
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                    Textarea::make('notes')
                        ->label('Catatan Admin (Internal)')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
