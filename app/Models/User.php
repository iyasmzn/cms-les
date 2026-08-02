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
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone', 'password', 'google_id', 'avatar'])]
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
    public const PANEL_ROLES = ['super_admin', 'panel_user', 'author', 'author_super', 'instructor'];

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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Course registrations this user submitted while logged in.
     *
     * @return HasMany<GroupMember, $this>
     */
    public function courseRegistrations(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function isAuthor(): bool
    {
        return $this->hasRole('author');
    }

    /**
     * The coach profile this account is attached to, if any.
     *
     * @return HasOne<Teacher, $this>
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Whether this account is attached to a coach profile. Holding the role
     * alone is not enough — without a linked Teacher there are no groups to
     * scope them to, so they would see nothing.
     */
    public function isInstructor(): bool
    {
        return $this->teacher()->exists();
    }

    /**
     * IDs of the groups this account coaches. Empty for everyone else.
     *
     * @return Collection<int, int>
     */
    public function coachedGroupIds(): Collection
    {
        return Group::query()
            ->whereIn('teacher_id', Teacher::query()->where('user_id', $this->getKey())->select('id'))
            ->pluck('id');
    }

    /**
     * Whether this account coaches the given group.
     */
    public function coaches(Group $group): bool
    {
        return $group->teacher_id !== null
            && Teacher::query()
                ->whereKey($group->teacher_id)
                ->where('user_id', $this->getKey())
                ->exists();
    }
}
