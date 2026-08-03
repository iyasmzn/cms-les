<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Teacher;
use App\Models\User;
use App\Services\InstructorAccountService;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->createAccountAction(),
            $this->resetPasswordAction(),
            $this->openAccountAction(),
            EditAction::make(),
        ];
    }

    /**
     * Membuatkan akun panel dari email guru. Hanya muncul saat guru belum
     * punya akun tertaut, dan hanya untuk admin yang boleh membuat user.
     */
    private function createAccountAction(): Action
    {
        return Action::make('createAccount')
            ->label('Buat Akun Panel')
            ->icon(Heroicon::OutlinedUserPlus)
            ->color('success')
            ->visible(fn (Teacher $record): bool => $record->user_id === null
                && auth()->user()?->can('create', User::class))
            ->modalHeading('Buat Akun Panel untuk Guru')
            ->modalDescription('Akun dibuat dari email guru, lalu otomatis diberi role instructor + panel_user agar bisa login ke panel admin.')
            ->modalSubmitActionLabel('Buat Akun')
            ->fillForm(fn (Teacher $record): array => ['email' => $record->email])
            ->schema([
                TextInput::make('email')
                    ->label('Email Akun')
                    ->email()
                    ->required()
                    ->maxLength(150)
                    ->helperText('Dipakai sebagai email login. Jika sudah ada user dengan email ini, akun tersebut yang ditautkan — tidak dibuat ganda.')
                    ->rule(fn (Teacher $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                        $existing = User::query()->firstWhere('email', $value);

                        if ($existing && Teacher::query()
                            ->where('user_id', $existing->getKey())
                            ->whereKeyNot($record->getKey())
                            ->exists()
                        ) {
                            $fail('Akun dengan email ini sudah ditautkan ke guru lain.');
                        }
                    }),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->maxLength(255)
                    ->helperText('Kosongkan untuk memakai password default: '.app(InstructorAccountService::class)->defaultPassword()),
            ])
            ->action(function (array $data, Teacher $record): void {
                $password = filled($data['password'] ?? null) ? $data['password'] : null;

                $user = app(InstructorAccountService::class)
                    ->provisionFor($record, $data['email'], $password);

                $record->refresh();

                Notification::make()
                    ->title($user->wasRecentlyCreated ? 'Akun panel dibuat' : 'Akun panel ditautkan')
                    ->body($this->accountNotificationBody($user, $password))
                    ->success()
                    ->persistent()
                    ->send();
            });
    }

    private function resetPasswordAction(): Action
    {
        return Action::make('resetPassword')
            ->label('Reset Password')
            ->icon(Heroicon::OutlinedKey)
            ->color('warning')
            ->visible(fn (Teacher $record): bool => $record->user_id !== null
                && auth()->user()?->can('update', $record->user))
            ->modalHeading('Reset Password Akun')
            ->modalDescription('Password lama akan diganti. Sampaikan password baru ke guru yang bersangkutan.')
            ->modalSubmitActionLabel('Reset')
            ->schema([
                TextInput::make('password')
                    ->label('Password Baru')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->maxLength(255)
                    ->helperText('Kosongkan untuk memakai password default: '.app(InstructorAccountService::class)->defaultPassword()),
            ])
            ->action(function (array $data, Teacher $record): void {
                $plainPassword = app(InstructorAccountService::class)
                    ->resetPassword($record->user, filled($data['password'] ?? null) ? $data['password'] : null);

                Notification::make()
                    ->title('Password direset')
                    ->body("Password baru untuk {$record->user->email}: {$plainPassword}")
                    ->success()
                    ->persistent()
                    ->send();
            });
    }

    private function openAccountAction(): Action
    {
        return Action::make('openAccount')
            ->label('Kelola Akun')
            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
            ->color('gray')
            ->visible(fn (Teacher $record): bool => $record->user_id !== null
                && auth()->user()?->can('update', $record->user))
            ->url(fn (Teacher $record): string => UserResource::getUrl('edit', ['record' => $record->user_id]));
    }

    private function accountNotificationBody(User $user, ?string $password): string
    {
        if (! $user->wasRecentlyCreated) {
            return "Akun {$user->email} sudah ada sebelumnya, kini ditautkan ke guru ini dan diberi role instructor + panel_user.";
        }

        if (filled($password)) {
            return "Akun {$user->email} dibuat dengan password yang Anda isi. Role instructor + panel_user sudah diberikan.";
        }

        return "Akun {$user->email} dibuat dengan password default: ".app(InstructorAccountService::class)->defaultPassword().'. Minta guru menggantinya setelah login pertama.';
    }
}
