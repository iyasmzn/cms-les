<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Roles that grant access to the admin panel.
     *
     * @var list<string>
     */
    public const PANEL_ROLES = ['super_admin', 'panel_user', 'author', 'author_super'];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(self::PANEL_ROLES);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * URL avatar — file di storage jika di-upload, URL penuh dari Google,
     * atau fallback ke avatar yang dibuat dari inisial nama.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (blank($this->avatar)) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=08484A&color=fff&size=200&bold=true';
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return asset('storage/'.$this->avatar);
    }

    public function authorRequest(): HasOne
    {
        return $this->hasOne(AuthorRequest::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isAuthor(): bool
    {
        return $this->hasRole('author');
    }

    public function hasPendingAuthorRequest(): bool
    {
        return $this->authorRequest()->where('status', 'pending')->exists();
    }
}
