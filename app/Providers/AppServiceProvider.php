<?php

namespace App\Providers;

use App\Listeners\ClaimGuestCourseRegistrations;
use App\Models\Event;
use App\Models\Institution;
use App\Models\Post;
use App\Models\Product;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\StaticPage;
use App\Models\Teacher;
use App\Observers\PostObserver;
use App\Observers\SlideObserver;
use App\Observers\TeacherObserver;
use App\Services\SitemapBuilder;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event as EventFacade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── Prevent ImageColumn from checking file existence on every table load ──
        ImageColumn::configureUsing(function (ImageColumn $column): void {
            $column->checkFileExistence(false);
        });

        // ── Auto-sync uploads to Media Library ───────────────────────────────────
        Slide::observe(SlideObserver::class);
        Teacher::observe(TeacherObserver::class);
        Post::observe(PostObserver::class);

        $this->configureMailFromSettings();
        $this->localizeEmailVerification();
        $this->invalidateSitemapCacheOnContentChange();

        // Attach guest course registrations to a member when they log in.
        EventFacade::listen(Login::class, ClaimGuestCourseRegistrations::class);
    }

    /**
     * Forget the cached sitemap whenever content that appears in it is created,
     * updated, or deleted, so /sitemap.xml regenerates on the next request.
     */
    private function invalidateSitemapCacheOnContentChange(): void
    {
        $forget = fn (Model $model) => Cache::forget(SitemapBuilder::CACHE_KEY);

        foreach ([
            Post::class,
            Event::class,
            Program::class,
            StaticPage::class,
            Institution::class,
            Product::class,
        ] as $model) {
            $model::saved($forget);
            $model::deleted($forget);
        }

        /*
         * Feature toggles decide which pages exist at all — turning off `toko`
         * makes the product routes 404. Without this hook the cached XML would
         * keep advertising those URLs to crawlers indefinitely, since no content
         * model is touched when a setting changes.
         */
        Setting::saved($forget);
        Setting::deleted($forget);
    }

    /**
     * Terapkan kredensial SMTP yang disimpan admin lewat panel (model Setting)
     * ke konfigurasi mail runtime. Jika mail_enabled mati atau tabel settings
     * belum ada (mis. saat migrate awal), biarkan default config/mail.php.
     */
    private function configureMailFromSettings(): void
    {
        try {
            if (! (bool) Setting::get('mail_enabled', false)) {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $host = Setting::get('mail_host');

        if (blank($host)) {
            return;
        }

        // ssl → koneksi implisit (port 465); selain itu biarkan Symfony
        // menegosiasikan STARTTLS otomatis (port 587).
        $scheme = Setting::get('mail_encryption') === 'ssl' ? 'smtps' : null;

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => (int) Setting::get('mail_port', 587),
            'mail.mailers.smtp.username' => Setting::get('mail_username'),
            'mail.mailers.smtp.password' => Setting::get('mail_password'),
            'mail.from.address' => Setting::get('mail_from_address') ?: config('mail.from.address'),
            'mail.from.name' => Setting::get('mail_from_name') ?: config('mail.from.name'),
        ]);
    }

    /**
     * Ganti isi email verifikasi bawaan Laravel menjadi Bahasa Indonesia.
     */
    private function localizeEmailVerification(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email Anda')
                ->greeting('Assalamu\'alaikum, '.($notifiable->name ?? '').'!')
                ->line('Terima kasih telah mendaftar. Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email', $url)
                ->line('Jika Anda tidak membuat akun ini, abaikan saja email ini.')
                ->salutation('Salam, '.config('app.name'));
        });
    }
}
