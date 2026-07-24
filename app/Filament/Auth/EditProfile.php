<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class EditProfile extends BaseEditProfile
{
    public function getMaxWidth(): Width
    {
        return Width::SixExtraLarge;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun')
                    ->description('Perbarui foto, nama, dan alamat email yang tampil di panel admin.')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->aside()
                    ->schema([
                        $this->getAvatarFormComponent(),
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ]),

                Section::make('Detail Akun')
                    ->description('Ringkasan status dan hak akses akun Anda.')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->aside()
                    ->schema([
                        Grid::make(2)->schema([
                            $this->getRolesPlaceholder(),
                            $this->getLoginMethodPlaceholder(),
                            $this->getJoinedAtPlaceholder(),
                            $this->getEmailStatusPlaceholder(),
                        ]),
                    ]),

                Section::make('Keamanan')
                    ->description('Ubah kata sandi Anda. Biarkan kosong bila tidak ingin menggantinya.')
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->aside()
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ]),
            ]);
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar')
            ->label('Foto Profil')
            ->image()
            ->avatar()
            ->disk('public')
            ->directory('avatars')
            ->visibility('public')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->maxSize(8192)
            ->helperText('JPG, PNG, WEBP, atau GIF. Maks 8MB.');
    }

    protected function getRolesPlaceholder(): Placeholder
    {
        return Placeholder::make('roles')
            ->label('Peran')
            ->content(function (): HtmlString {
                /** @var User $user */
                $user = $this->getUser();

                $badges = $user->getRoleNames()
                    ->map(fn (string $role): string => '<span style="display:inline-block;font-size:.7rem;font-weight:600;padding:.15rem .55rem;border-radius:9999px;background:rgba(8,72,74,.12);color:#08484A;text-transform:capitalize;margin:.1rem .25rem .1rem 0;">'.e(str_replace('_', ' ', $role)).'</span>')
                    ->implode('');

                return new HtmlString($badges !== '' ? $badges : '<span style="opacity:.6;">Tidak ada peran</span>');
            });
    }

    protected function getLoginMethodPlaceholder(): Placeholder
    {
        /** @var User $user */
        $user = $this->getUser();

        return Placeholder::make('login_method')
            ->label('Metode Masuk')
            ->content(filled($user->google_id) ? 'Google' : 'Email & Kata Sandi');
    }

    protected function getJoinedAtPlaceholder(): Placeholder
    {
        /** @var User $user */
        $user = $this->getUser();

        return Placeholder::make('joined_at')
            ->label('Bergabung Sejak')
            ->content($user->created_at?->timezone('Asia/Jakarta')->translatedFormat('d F Y') ?? '—');
    }

    protected function getEmailStatusPlaceholder(): Placeholder
    {
        /** @var User $user */
        $user = $this->getUser();

        return Placeholder::make('email_status')
            ->label('Status Email')
            ->content(new HtmlString(
                $user->email_verified_at !== null
                    ? '<span style="color:#059669;font-weight:600;">Terverifikasi</span>'.
                        ' <span style="opacity:.6;">· '.e($user->email_verified_at->timezone('Asia/Jakarta')->translatedFormat('d M Y')).'</span>'
                    : '<span style="color:#d97706;font-weight:600;">Belum diverifikasi</span>'
            ));
    }
}
