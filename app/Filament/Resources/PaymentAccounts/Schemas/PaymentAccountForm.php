<?php

namespace App\Filament\Resources\PaymentAccounts\Schemas;

use App\Models\PaymentAccount;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PaymentAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment Destination')
                ->description('Shown to members when they settle a course bill. Shared by every course and group.')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('type')
                            ->label('Type')
                            ->options(PaymentAccount::typeOptions())
                            ->default('bank')
                            ->required()
                            ->native(false)
                            ->live(),

                        TextInput::make('label')
                            ->label('Label')
                            ->maxLength(120)
                            ->placeholder(fn (Get $get): string => $get('type') === 'qris' ? 'QRIS' : 'e.g. BCA — Main Account')
                            ->helperText('Optional. Falls back to the bank name.'),
                    ]),

                    Grid::make(3)
                        ->schema([
                            TextInput::make('bank_name')
                                ->label('Bank')
                                ->required()
                                ->maxLength(80)
                                ->placeholder('BCA'),

                            TextInput::make('account_number')
                                ->label('Account Number')
                                ->required()
                                ->maxLength(40),

                            TextInput::make('account_holder')
                                ->label('Account Holder')
                                ->required()
                                ->maxLength(120),
                        ])
                        ->visible(fn (Get $get): bool => $get('type') === 'bank'),

                    FileUpload::make('qris_image')
                        ->label('QRIS Image')
                        ->image()
                        ->disk('public')
                        ->directory('payment-accounts')
                        ->visibility('public')
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        // Resized in the browser like every other panel upload,
                        // but kept generous so the QR stays scannable.
                        ->automaticallyResizeImagesToWidth('1200')
                        ->maxSize(4096)
                        ->required()
                        ->helperText('The QR code members scan. Keep it sharp — it is shown full width on mobile.')
                        ->visible(fn (Get $get): bool => $get('type') === 'qris')
                        ->columnSpanFull(),

                    Textarea::make('instructions')
                        ->label('Instructions')
                        ->rows(2)
                        ->maxLength(500)
                        ->placeholder('e.g. Put your registration number in the transfer note.')
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('sort_order')
                            ->label('Order')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->onColor('success')
                            ->helperText('Inactive destinations are hidden from members.'),
                    ]),
                ]),
        ]);
    }
}
