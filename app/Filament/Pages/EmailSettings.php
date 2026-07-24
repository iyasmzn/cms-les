<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class EmailSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament.pages.general-settings';

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Email & SMTP';

    protected static ?string $title = 'Pengaturan Email & SMTP';

    protected static ?int $navigationSort = 11;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'mail_enabled' => (bool) Setting::get('mail_enabled', false),
            'mail_host' => Setting::get('mail_host'),
            'mail_port' => Setting::get('mail_port', 587),
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_username' => Setting::get('mail_username'),
            'mail_from_address' => Setting::get('mail_from_address'),
            'mail_from_name' => Setting::get('mail_from_name', config('app.name')),
        ]);
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Server Email (SMTP)')
                ->description('Diperlukan agar website benar-benar mengirim email (verifikasi akun, reset kata sandi, notifikasi). Jika dimatikan, email hanya ditulis ke log dan tidak terkirim ke inbox. Gunakan kredensial dari penyedia SMTP seperti Gmail, Mailtrap, Resend, atau Postmark.')
                ->icon(Heroicon::OutlinedEnvelope)
                ->schema([
                    Toggle::make('mail_enabled')
                        ->label('Aktifkan pengiriman email via SMTP')
                        ->helperText('Bila mati, aplikasi memakai driver "log" (email tidak terkirim).')
                        ->onColor('success')
                        ->offColor('danger')
                        ->live()
                        ->columnSpanFull(),

                    Grid::make(2)->schema([
                        TextInput::make('mail_host')
                            ->label('SMTP Host')
                            ->placeholder('smtp.gmail.com')
                            ->required(fn (Get $get): bool => (bool) $get('mail_enabled'))
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('mail_port')
                            ->label('Port')
                            ->numeric()
                            ->placeholder('587')
                            ->required(fn (Get $get): bool => (bool) $get('mail_enabled'))
                            ->columnSpan(1),

                        Select::make('mail_encryption')
                            ->label('Enkripsi')
                            ->options([
                                'tls' => 'TLS / STARTTLS (port 587)',
                                'ssl' => 'SSL (port 465)',
                                'none' => 'Tanpa enkripsi',
                            ])
                            ->default('tls')
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('mail_username')
                            ->label('Username')
                            ->placeholder('akun@gmail.com')
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('mail_password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->placeholder('Isi untuk mengubah password yang tersimpan')
                            ->hint('Disimpan aman di server. Kosongkan jika tidak ingin mengubah.')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->visible(fn (Get $get): bool => (bool) $get('mail_enabled'))
                        ->columnSpanFull(),
                ]),

            Section::make('Identitas Pengirim')
                ->description('Nama dan alamat yang tampil sebagai pengirim email.')
                ->icon(Heroicon::OutlinedIdentification)
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('mail_from_address')
                            ->label('Alamat Pengirim (From)')
                            ->email()
                            ->placeholder('noreply@sekolah.sch.id')
                            ->maxLength(255)
                            ->columnSpan(1),

                        TextInput::make('mail_from_name')
                            ->label('Nama Pengirim')
                            ->placeholder(config('app.name'))
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])->columnSpanFull(),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $toSave = [
            'mail_enabled' => $data['mail_enabled'] ?? false,
            'mail_host' => $data['mail_host'] ?? null,
            'mail_port' => $data['mail_port'] ?? 587,
            'mail_encryption' => $data['mail_encryption'] ?? 'tls',
            'mail_username' => $data['mail_username'] ?? null,
            'mail_from_address' => $data['mail_from_address'] ?? null,
            'mail_from_name' => $data['mail_from_name'] ?? null,
        ];

        // Hanya simpan password bila diisi.
        if (! blank($data['mail_password'] ?? null)) {
            $toSave['mail_password'] = $data['mail_password'];
        }

        Setting::setMany($toSave);

        Notification::make()
            ->success()
            ->title('Pengaturan email berhasil disimpan')
            ->send();
    }

    public function sendTestEmail(string $recipient): void
    {
        $this->save();

        try {
            Mail::raw('Ini adalah email uji dari '.config('app.name').'. Jika Anda menerima pesan ini, konfigurasi SMTP sudah benar.', function ($message) use ($recipient) {
                $message->to($recipient)->subject('Email Uji — '.config('app.name'));
            });

            Notification::make()
                ->success()
                ->title('Email uji terkirim')
                ->body('Cek inbox '.$recipient.' (termasuk folder spam).')
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Gagal mengirim email uji')
                ->body($e->getMessage())
                ->persistent()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTest')
                ->label('Kirim Email Uji')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Simpan pengaturan saat ini lalu kirim email uji ke alamat tujuan.')
                ->schema([
                    TextInput::make('test_recipient')
                        ->label('Kirim ke alamat')
                        ->email()
                        ->required()
                        ->default(fn (): ?string => auth()->user()?->email)
                        ->helperText('Default ke email akun Anda. Ubah untuk mengirim ke alamat lain.'),
                ])
                ->action(function (array $data): void {
                    $this->sendTestEmail($data['test_recipient']);
                }),

            Action::make('save')
                ->label('Simpan Pengaturan')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->action('save'),
        ];
    }
}
