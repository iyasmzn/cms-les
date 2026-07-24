<?php

namespace App\Filament\Resources\AuthorRequests\Pages;

use App\Filament\Resources\AuthorRequests\AuthorRequestResource;
use App\Models\AuthorRequest;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAuthorRequest extends EditRecord
{
    protected static string $resource = AuthorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $original = $this->record->status;

        if ($data['status'] !== $original) {
            $data['reviewed_by'] = Auth::id();
            $data['reviewed_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        /** @var AuthorRequest $record */
        $record = $this->record->fresh();

        if ($record->status === 'approved') {
            $record->user->assignRole('author');
        } elseif ($record->status === 'rejected') {
            $record->user->removeRole('author');
        }
    }
}
