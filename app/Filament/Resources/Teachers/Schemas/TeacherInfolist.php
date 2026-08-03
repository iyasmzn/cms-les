<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Models\Teacher;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profil Guru')
                ->schema([
                    ImageEntry::make('photo')
                        ->label('Foto')
                        ->disk('public')
                        ->circular()
                        ->height(96)
                        ->defaultImageUrl(fn (Teacher $record): string => $record->photo_url)
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight('bold'),

                        TextEntry::make('nip')
                            ->label('NIP')
                            ->placeholder('—')
                            ->copyable(),

                        TextEntry::make('position')
                            ->label('Jabatan')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('subject')
                            ->label('Mata Pelajaran')
                            ->placeholder('—'),

                        TextEntry::make('education')
                            ->label('Pendidikan Terakhir')
                            ->placeholder('—'),

                        TextEntry::make('institution.name')
                            ->label('Instansi / Unit')
                            ->badge()
                            ->color('gray')
                            ->placeholder('Tidak terikat unit'),

                        IconEntry::make('is_active')
                            ->label('Aktif Mengajar')
                            ->boolean(),

                        TextEntry::make('groups_count')
                            ->label('Kelompok Dilatih')
                            ->state(fn (Teacher $record): int => $record->groups()->count())
                            ->badge()
                            ->color('info'),
                    ]),
                ]),

            Section::make('Kontak')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('—')
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label('Nomor Telepon')
                            ->placeholder('—'),

                        TextEntry::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('—'),
                    ]),
                ]),

            Section::make('Akun Panel')
                ->description('Akun login guru ini ke panel admin sebagai instruktur.')
                ->icon(Heroicon::OutlinedKey)
                ->schema([
                    Text::make(fn (Teacher $record): string => blank($record->email)
                        ? 'Guru ini belum punya akun panel. Isi dulu email guru pada tombol "Buat Akun Panel" di kanan atas untuk membuatkannya.'
                        : 'Guru ini belum punya akun panel. Klik tombol "Buat Akun Panel" di kanan atas — akun dibuat dari email guru dan langsung mendapat role instructor + panel_user.')
                        ->visible(fn (Teacher $record): bool => $record->user_id === null),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('user.name')
                                ->label('Nama Akun')
                                ->weight('bold'),

                            TextEntry::make('user.email')
                                ->label('Email Login')
                                ->copyable(),

                            TextEntry::make('user.roles.name')
                                ->label('Role')
                                ->badge()
                                ->color('success')
                                ->placeholder('Belum punya role'),

                            TextEntry::make('user.email_verified_at')
                                ->label('Email Terverifikasi')
                                ->dateTime('d M Y H:i')
                                ->placeholder('Belum diverifikasi'),

                            TextEntry::make('user.created_at')
                                ->label('Akun Dibuat')
                                ->dateTime('d M Y H:i'),

                            TextEntry::make('user.updated_at')
                                ->label('Terakhir Diperbarui')
                                ->since(),
                        ])
                        ->visible(fn (Teacher $record): bool => $record->user_id !== null),
                ]),
        ]);
    }
}
