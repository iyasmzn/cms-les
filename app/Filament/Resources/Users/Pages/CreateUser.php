<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CreateUser extends CreateRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return self::applyImagePickers(
            $data,
            ['avatar'],
            self::imageBaseName($data['name'] ?? null, 'Avatar'),
        );
    }

    /**
     * `email_verified_at` tidak mass-assignable pada User, jadi harus di-set
     * lewat `forceFill()` agar toggle verifikasi email benar-benar tersimpan.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $verifiedAt = Arr::pull($data, 'email_verified_at');

        $record = parent::handleRecordCreation($data);

        if (filled($verifiedAt)) {
            $record->forceFill(['email_verified_at' => $verifiedAt])->save();
        }

        return $record;
    }
}
