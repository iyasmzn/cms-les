<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Membuat & mengelola akun panel (User) untuk guru / instruktur.
 *
 * Dipakai dari halaman Guru di panel admin: satu tombol membuat akun dari
 * email guru, memberi role yang dibutuhkan, lalu menautkannya ke profil guru.
 */
class InstructorAccountService
{
    /**
     * Role yang diberikan ke akun guru: `panel_user` membuka akses panel,
     * `instructor` membatasi datanya ke kelompok yang dia latih.
     *
     * @var list<string>
     */
    public const ROLES = ['instructor', 'panel_user'];

    /**
     * Buat (atau tautkan) akun panel untuk seorang guru.
     *
     * Jika sudah ada user dengan email tersebut, user itu yang dipakai —
     * tidak membuat duplikat — dan passwordnya hanya diubah bila diisi.
     */
    public function provisionFor(Teacher $teacher, string $email, ?string $password = null): User
    {
        $user = User::query()->firstWhere('email', $email);

        if ($user === null) {
            $user = new User;

            // `email_verified_at` tidak mass-assignable; akun dibuat admin jadi
            // langsung dianggap terverifikasi. Cast `hashed` yang meng-hash password.
            $user->forceFill([
                'name' => $teacher->name,
                'email' => $email,
                'password' => $password ?? $this->defaultPassword(),
                'email_verified_at' => now(),
            ])->save();
        } elseif (filled($password)) {
            $user->forceFill(['password' => $password])->save();
        }

        $this->grantInstructorRoles($user);

        $teacher->forceFill([
            'user_id' => $user->getKey(),
            'email' => $teacher->email ?: $email,
        ])->save();

        return $user;
    }

    /**
     * Pastikan akun punya role instructor + panel_user (role dibuat bila belum ada).
     */
    public function grantInstructorRoles(User $user): void
    {
        $roles = array_map(
            fn (string $name): Role => Role::findOrCreate($name, 'web'),
            self::ROLES,
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user->assignRole($roles);
    }

    /**
     * Setel ulang password akun dan kembalikan password polosnya agar bisa
     * ditampilkan sekali ke admin.
     */
    public function resetPassword(User $user, ?string $password = null): string
    {
        $plainPassword = $password ?: $this->defaultPassword();

        $user->forceFill(['password' => $plainPassword])->save();

        return $plainPassword;
    }

    /**
     * Password bawaan saat admin mengosongkan kolom password.
     */
    public function defaultPassword(): string
    {
        return (string) config('auth.instructor_default_password');
    }
}
