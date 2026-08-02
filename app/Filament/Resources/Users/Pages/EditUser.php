<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\InteractsWithImagePicker;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    use InteractsWithImagePicker;

    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
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
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (array_key_exists('email_verified_at', $data)) {
            $record->forceFill(['email_verified_at' => Arr::pull($data, 'email_verified_at')]);
        }

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
