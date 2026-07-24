<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Foto Profil')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->avatar()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(8192)
                            ->helperText('JPG, PNG, WEBP, atau GIF. Maks 8MB.')
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                        ]),

                        Toggle::make('email_verified_at')
                            ->label('Email Terverifikasi')
                            ->onColor('success')
                            ->offColor('danger')
                            ->dehydrateStateUsing(fn (bool $state): ?string => $state ? now()->toDateTimeString() : null)
                            ->afterStateHydrated(fn ($component, $state) => $component->state(filled($state))),
                    ]),

                Section::make('Keamanan')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->confirmed()
                                ->minLength(8),

                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->password()
                                ->revealable()
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->dehydrated(false)
                                ->minLength(8),
                        ]),
                    ])
                    ->description('Kosongkan jika tidak ingin mengubah password (saat edit).'),

                Section::make('Akses & Peran')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->helperText('Login ke panel admin hanya bisa dilakukan oleh user dengan salah satu role: '.implode(', ', User::PANEL_ROLES).'.')
                            ->hint(fn (?array $state): ?string => self::hasPanelAccessRole($state)
                                ? null
                                : 'User tidak akan bisa login ke panel')
                            ->hintColor('warning')
                            ->hintIcon(fn (?array $state): ?Heroicon => self::hasPanelAccessRole($state)
                                ? null
                                : Heroicon::ExclamationTriangle)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @param  array<int|string>|null  $roleIds
     */
    protected static function hasPanelAccessRole(?array $roleIds): bool
    {
        if (blank($roleIds)) {
            return false;
        }

        return Role::whereIn('id', $roleIds)
            ->whereIn('name', User::PANEL_ROLES)
            ->exists();
    }
}
