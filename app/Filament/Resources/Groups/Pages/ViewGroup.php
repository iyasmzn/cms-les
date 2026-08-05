<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Groups\GroupResource;
use App\Models\Group;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    /**
     * Deliberately minimal: the active/inactive toggle plus an explicit Edit
     * button. Opening a group lands here rather than straight in the form, so
     * editing stays a deliberate click.
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->toggleActiveAction(),
            EditAction::make(),
        ];
    }

    private function toggleActiveAction(): Action
    {
        return Action::make('toggleActive')
            ->label(fn (Group $record): string => $record->is_active ? 'Deactivate' : 'Activate')
            ->icon(fn (Group $record): Heroicon => $record->is_active ? Heroicon::OutlinedXCircle : Heroicon::OutlinedCheckCircle)
            ->color(fn (Group $record): string => $record->is_active ? 'danger' : 'success')
            ->requiresConfirmation()
            ->modalDescription(fn (Group $record): string => $record->is_active
                ? 'The group stays listed in the panel but stops accepting new members.'
                : 'The group starts accepting new members again, up to its capacity.')
            ->visible(fn (Group $record): bool => auth()->user()?->can('update', $record) ?? false)
            ->action(function (Group $record): void {
                $record->update(['is_active' => ! $record->is_active]);

                Notification::make()
                    ->success()
                    ->title($record->is_active ? 'Group activated' : 'Group deactivated')
                    ->send();
            });
    }
}
