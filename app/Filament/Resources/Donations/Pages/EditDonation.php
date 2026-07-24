<?php

namespace App\Filament\Resources\Donations\Pages;

use App\Filament\Resources\Donations\DonationResource;
use App\Models\Donation;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDonation extends EditRecord
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Donation $record */
        $record = $this->getRecord();

        if ($data['status'] === 'confirmed' && $record->status !== 'confirmed') {
            $data['confirmed_at'] = now();
        }

        if ($data['status'] !== 'confirmed') {
            $data['confirmed_at'] = null;
        }

        return $data;
    }
}
